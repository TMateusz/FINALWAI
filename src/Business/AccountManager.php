<?php
    
    class AccountManager{
        private $userModel;
        private $userManager;
        private $imageManager;

        public function __construct($userModel = null, $userManager = null, $imageManager = null){
            $this->userModel = $userModel ?? new UserModel();
            $this->userManager = $userManager ?? new UserManager($this->userModel);
            $this->imageManager = $imageManager ?? new ImageManager(new ImageModel());
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
            return ['user_id' => $userId, 'login' => $login];
        }

        public function logout(){
            return true;
        }

        public function isLoggedIn(array $session){
            return isset($session['user_id']);
        }

        public function getCurrentUser($login = null){
            if ($login) {
                return $this->userModel->getByLogin($login);
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
                return ["Użytownik z takim e-mailem/loginem już istnieje"];
            }
            $passwordHash = UserManager::hashPassword($password1);
            $crated=$this->userModel->create($login, $passwordHash, $email);
            if (!$crated){
                return ["Błąd bazy danych"];  
            }

            $uploadResult = $this->imageManager->uploadProfileImage($login, $profilePhotoFile);
            $warning = null;
            if ($uploadResult === false || is_array($uploadResult)){
                $warning = is_array($uploadResult) ? implode(' ', $uploadResult) : 'Blad zapisu zdjęcia profilowego.';
            }

            return $warning ? ['success' => true, 'warning' => $warning] : true;
        }
    }