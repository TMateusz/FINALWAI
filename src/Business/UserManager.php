<?php
    require_once __DIR__ . '/../Models/UserModel.php';

    class UserManager{
        private $userModel;
        public function __construct(){
            $this->userModel = new UserModel();
        }
        public static function validateUserData($email, $login, $password1, $password2){
            $errors = [];

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors[] = "Niepoprawny format adresu e-mail.";
            }
            if ($password1 !== $password2){
                $errors[] = "Hasła nie są zgodne.";
            }
            return empty($errors) ? true : $errors;
        }
        public static function hashPassword($password){
            return password_hash($password, PASSWORD_DEFAULT);
        }

        public function verifyPassword($password, $hashedPassword){
            return password_verify($password, $hashedPassword);
        }   

        public function userExists($email, $login){
            $user = $this->userModel->getByLogin($login);
            return $user !== null;
        }
    }