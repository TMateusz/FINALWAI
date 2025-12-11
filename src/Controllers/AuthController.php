<?php

include_once __DIR__ . '/../Models/UserModel.php';
include_once __DIR__ . '/../Business/UserManager.php';
include_once __DIR__ . '/../Business/AccountManager.php';
include_once __DIR__ . '/../Controllers/BaseController.php';

    class AuthController extends BaseController{
        private $accountManager;

        public function __construct(){
            $this->accountManager = new AccountManager();
        }

        public function login(){
            $data=['error' => null];
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login']) && isset($_POST['password1']) && isset($_POST['submit'])){
                $login = $_POST['login'];
                $password = $_POST['password1'];
                $user = $this->accountManager->authenticate($login, $password);
                if ($user){
                    $this->accountManager->login($user['id'], $user['login']);
                    return $this->redirect('/gallery');
                } else {
                    $data['error'] = "Błędny login lub hasło.";
                }
            }
            return $this->render('login_view', $data);
        }

        public function register(){
            $data=['error' => null, 'success' => null];
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address_email']) && isset($_POST['login']) && isset($_POST['password1']) && isset($_POST['password2']) && isset($_POST['submit'])){
                $email = $_POST['address_email'];
                $login = $_POST['login'];
                $password1 = $_POST['password1'];
                $password2 = $_POST['password2'];
                // profile file is required by the form; forward it directly from $_FILES
                $profileFile = $_FILES['fileToUpload'];

                $result = $this->accountManager->register($email, $login, $password1, $password2, $profileFile);
                if ($result === true){
                    $data=['success' => "Rejestracja zakończona sukcesem. Możesz się teraz zalogować."];
                    return $this->redirect('/login');
                } else {
                    $data['error'] = is_array($result) ? implode(' ', $result) : $result;
                }
                
            }
            return $this->render('register_view', $data);
        }

        public function logout(){
            $this->accountManager->logout();
            return $this->redirect('/gallery');
        }
    }