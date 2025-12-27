<?php
    // Załącz:

    include_once __DIR__. '/../Controllers/BaseController.php';

    class GalleryController extends BaseController{
        private $imageManager;
        private $accountManager;

        public function __construct(){
            $imageModel = new ImageModel();
            $userModel = new UserModel();
            $this->imageManager = new ImageManager($imageModel);
            $this->accountManager = new AccountManager($userModel, new UserManager($userModel));
        }
        
        public function index(){
            $login = session_get('login', null);
            $images = $this->imageManager->getAllImages($login);
            
            $photoPerPage = 3;
            $totalPhotos = count($images);
            $totalPages = ceil($totalPhotos / $photoPerPage);
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($currentPage < 1) $currentPage = 1;
            if ($currentPage > $totalPages) $currentPage = $totalPages;
            $startIndex = ($currentPage - 1) * $photoPerPage;
            $imagesToShow = array_slice($images, $startIndex, $photoPerPage);
            
            // Pobierz koszyk z sesji (jeśli istnieje)
            $cart = session_get('cart', []);
            
            $data = [
                'images' => $imagesToShow,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'cart' => $cart
            ];
            return $this->render('gallery_view_new', $data);
        }

        public function saveCart(){
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                $posted = isset($_POST['cart']) && is_array($_POST['cart']) ? $_POST['cart'] : [];
                $existing = session_get('cart', []);
                if (!is_array($existing)) $existing = [];

                // Merge posted selections with existing cart so selections across pages are preserved
                $merged = array_values(array_unique(array_merge($existing, $posted)));
                session_set('cart', $merged);

                $quantities = session_get('quantities', []);
                if (!is_array($quantities)) $quantities = [];

                // Ensure default quantity 1 for all items in merged cart
                foreach ($merged as $file) {
                    if (!isset($quantities[$file]) || (int)$quantities[$file] <= 0) {
                        $quantities[$file] = 1;
                    }
                }

                session_set('quantities', $quantities);
            }

            return $this->redirect('/gallery');
        }

        /**
         * AJAX endpoint to update single file selection in session cart.
         * Expects POST: file=<filename>&checked=0|1
         */
        public function updateCart(){
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {    
                http_response_code(405);
                echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
                return;
            }

            $file = $_POST['file'] ?? null;
            $checked = isset($_POST['checked']) ? (bool)intval($_POST['checked']) : null;

            if (!$file || $checked === null){
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'Missing parameters']);
                return;
            }

            $cart = session_get('cart', []);
            if (!is_array($cart)) $cart = [];

            if ($checked){
                if (!in_array($file, $cart)) $cart[] = $file;
            } else {
                $index = array_search($file, $cart);
                if ($index !== false) array_splice($cart, $index, 1);
            }

            session_set('cart', $cart);

            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'cartCount' => count($cart)]);
        }
    }