<?php
    class UploadController extends BaseController{
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
            $user = $this->accountManager->getCurrentUser($login);
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