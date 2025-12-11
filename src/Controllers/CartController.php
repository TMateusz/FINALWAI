<?php
include_once __DIR__. '/../Controllers/BaseController.php';
include_once __DIR__. '/../Business/ImageManager.php';
include_once __DIR__. '/../Models/ImageModel.php';
include_once __DIR__. '/../Business/AccountManager.php';
include_once __DIR__. '/../Business/UserManager.php';
include_once __DIR__. '/../Models/UserModel.php';

class CartController extends BaseController{
    private $imageManager;
    private $accountManager;

    public function __construct(){
        $this->imageManager = new ImageManager();
        $this->accountManager = new AccountManager();
    }

    public function index(){ 
        $cartImages = $this->imageManager->getCart();
        $data = ['cartImages' => $cartImages];
        return $this->render('cart_view', $data);
    }

    public function remove(){
        // Aktualizuj ilości
        if (isset($_POST['quantity']) && isset($_POST['imageFile'])) {
            $quantities = $_POST['quantity'];
            $imageFiles = $_POST['imageFile'];
            $_SESSION['quantities'] = [];
            
            foreach ($imageFiles as $index => $file) {
                if (isset($quantities[$index]) && $quantities[$index] > 0) {
                    $_SESSION['quantities'][$file] = (int)$quantities[$index];
                }
            }
        }
        
        // Usuń zaznaczone elementy
        if (isset($_POST['toRemove'])) {
            $toRemove = $_POST['toRemove'];
            $cart = $_SESSION['cart'] ?? [];
            $_SESSION['cart'] = array_diff($cart, $toRemove);
            $_SESSION['cart'] = array_values($_SESSION['cart']);

            foreach ($toRemove as $imageFile) {
                unset($_SESSION['quantities'][$imageFile]);
            }
        }
        return $this->redirect('/cart');
    }
}
