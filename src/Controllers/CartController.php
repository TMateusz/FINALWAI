<?php
include_once __DIR__. '/../Controllers/BaseController.php';

class CartController extends BaseController{
    private $imageManager;
    private $accountManager;

    public function __construct(){
        $imageModel = new ImageModel();
        $userModel = new UserModel();
        $this->imageManager = new ImageManager($imageModel);
        $this->accountManager = new AccountManager($userModel, new UserManager($userModel));
    }

    public function index(){ 
        $cart = session_get('cart', []);
        $quantities = session_get('quantities', []);
        $login = session_get('login', null);
        $cartImages = $this->imageManager->getCart($cart, $quantities, $login);
        $data = ['cartImages' => $cartImages];
        return $this->render('cart_view', $data);
    }

    public function remove(){
        // Aktualizuj ilości
        if (isset($_POST['quantity']) && isset($_POST['imageFile'])) {
            $quantities = $_POST['quantity'];
            $imageFiles = $_POST['imageFile'];
            $sessQuant = [];
            foreach ($imageFiles as $index => $file) {
                if (isset($quantities[$index]) && $quantities[$index] > 0) {
                    $sessQuant[$file] = (int)$quantities[$index];
                }
            }
            session_set('quantities', $sessQuant);
        }
        
        // Usuń zaznaczone elementy
        if (isset($_POST['toRemove'])) {
            $toRemove = $_POST['toRemove'];
            $cart = session_get('cart', []);
            $newCart = array_values(array_diff($cart, $toRemove));
            session_set('cart', $newCart);

            $quantities = session_get('quantities', []);
            foreach ($toRemove as $imageFile) {
                if (isset($quantities[$imageFile])) unset($quantities[$imageFile]);
            }
            session_set('quantities', $quantities);
        }
        return $this->redirect('/cart');
    }
}
