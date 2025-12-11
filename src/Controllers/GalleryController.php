<?php
    // Załącz:

    include_once __DIR__. '/../Controllers/BaseController.php';
    include_once __DIR__. '/../Business/ImageManager.php';
    include_once __DIR__. '/../Models/ImageModel.php';
    include_once __DIR__. '/../Business/AccountManager.php';
    include_once __DIR__. '/../Business/UserManager.php';
    include_once __DIR__. '/../Models/UserModel.php';

    class GalleryController extends BaseController{
        private $imageManager;
        private $accountManager;

        public function __construct(){
            $this->imageManager = new ImageManager();
            $this->accountManager = new AccountManager();
        }
        
        public function index(){
            $user = $this->accountManager->getCurrentUser();
            $login = $user ? $user['login'] : null;
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
            $cart = $_SESSION['cart'] ?? [];
            
            $data = [
                'images' => $imagesToShow,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'cart' => $cart
            ];
            return $this->render('gallery_view_new', $data);
        }

        public function saveCart(){
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart'])){
                // Nadpisz koszyk prostą listą przesłaną przez formularz
                $_SESSION['cart'] = $_POST['cart'];

                if (!isset($_SESSION['quantities']) || !is_array($_SESSION['quantities'])) {
                    $_SESSION['quantities'] = [];
                }

                // Usuń quantities dla plików, które nie znajdują się już w koszyku
                foreach (array_keys($_SESSION['quantities']) as $file) {
                    if (!in_array($file, $_SESSION['cart'])) {
                        unset($_SESSION['quantities'][$file]);
                    }
                }

                // Zapewnij domyślną ilość 1 dla plików w koszyku
                foreach ($_SESSION['cart'] as $file) {
                    if (!isset($_SESSION['quantities'][$file]) || (int)$_SESSION['quantities'][$file] <= 0) {
                        $_SESSION['quantities'][$file] = 1;
                    }
                }
            } else {
                // Pusty koszyk -> wyczyść też quantities
                $_SESSION['cart'] = [];
                $_SESSION['quantities'] = [];
            }

            return $this->redirect('/gallery');
        }
    }