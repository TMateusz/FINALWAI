<?php
    require_once __DIR__ . '/../Models/UserModel.php';
    require_once __DIR__ . '/../Business/UserManager.php';

    class AccountManager{
        private $userModel;
        private $userManager;

        public function __construct(){
            $this->userModel = new UserModel();
            $this->userManager = new UserManager();
        }

        public function authenticate($login, $password){
            $user=$this->userModel->getByLogin($login);
            if (!$user){
                return false;
            }
            if ($this->userManager->verifyPassword($password, $user['password'])){
                return $user;
            }

            return false;
        }

        public function login($userId,$login){
            session_start();
            $_SESSION['user_id']=$userId;
            $_SESSION['login']=$login;
        }

        public function logout(){
            // Zachowaj koszyk przed wylogowaniem
            $cart = $_SESSION['cart'] ?? [];
            $quantities = $_SESSION['quantities'] ?? [];
            
            session_unset();
            session_destroy();
            
            // Rozpocznij nową sesję i przywróć koszyk
            session_start();
            $_SESSION['cart'] = $cart;
            $_SESSION['quantities'] = $quantities;
        }

        public function isLoggedIn(){
            return isset($_SESSION['user_id']);
        }

        public function getCurrentUser(){
            if (isset($_SESSION['login'])) {
                return $this->userModel->getByLogin($_SESSION['login']);
            }
            return null;
        }
        
        public function register($email, $login, $password1, $password2, $profilePhotoFile){
            $validation = UserManager::validateUserData($email, $login, $password1, $password2);
            if ($validation !== true){
                return $validation;
            }
            $exists = $this->userManager->userExists($email, $login);
            if ($exists){
                return ["User with this email or login already exists."];
            }
            $passwordHash = UserManager::hashPassword($password1);
            $crated=$this->userModel->create($login, $passwordHash, $email);
            if (!$crated){
                return ["Failed to create user due to a database error."];  
            }

            // Profile file is required by the form; save it using ImageManager.
            if (session_status() === PHP_SESSION_NONE) session_start();
            require_once __DIR__ . '/ImageManager.php';
            $imageManager = new ImageManager();
            $uploadResult = $imageManager->uploadProfileImage($login, $profilePhotoFile);
            if ($uploadResult === false || is_array($uploadResult)){
                // Save warning for UI but do not fail registration
                $_SESSION['profile_upload_warning'] = is_array($uploadResult) ? implode(' ', $uploadResult) : 'Failed to save profile image.';
            }

            return true;
        }
    }