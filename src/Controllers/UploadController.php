<?php
    include_once __DIR__. '/../Controllers/BaseController.php';
    include_once __DIR__. '/../Business/ImageManager.php';
    include_once __DIR__. '/../Models/ImageModel.php';
    include_once __DIR__. '/../Business/AccountManager.php';
    include_once __DIR__. '/../Business/UserManager.php';
    include_once __DIR__. '/../Models/UserModel.php';

    class UploadController extends BaseController{
        private $imageManager;
        private $accountManager;

        public function __construct(){
            $this->imageManager = new ImageManager();
            $this->accountManager = new AccountManager();
        }

        public function index(){
            $user = $this->accountManager->getCurrentUser();
            $authorDefault = (is_array($user) && isset($user['login'])) ? $user['login'] : '';
            $data = ['error' => null, 'success' => null, 'author' => $authorDefault];
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])){
                $title = $_POST['title'];
                $isPublic = isset($_POST['visibility']) ? (int)$_POST['visibility'] : 0;
                $file = $_FILES['fileToUpload'];
                $author = trim($_POST['author'] ?? $authorDefault);
                $result = $this->imageManager->uploadImage($title, $author, $file, $isPublic); 
                if ($result === true){
                    $data['success'] = "Zdjęcie zostało przesłane pomyślnie.";
                } else {
                    $data['error'] = is_array($result) ? implode(' ', $result) : $result;
                }
            }

            return $this->render('upload_view', $data);
        }
    }