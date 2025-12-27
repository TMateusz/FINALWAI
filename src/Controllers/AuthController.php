<?php
include_once __DIR__ . '/../Controllers/BaseController.php';

    class AuthController extends BaseController{
        private $accountManager;

        public function __construct(){
                $userModel = new UserModel();
                $this->accountManager = new AccountManager($userModel, new UserManager($userModel));
        }

        public function login(){
            $data=['error' => null];
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login']) && isset($_POST['password1']) && isset($_POST['submit'])){
                $login = $_POST['login'];
                $password = $_POST['password1'];
                $user = $this->accountManager->authenticate($login, $password);
                if ($user){
                    // Regenerate session id to prevent fixation and ensure new cookie
                    if (function_exists('session_regenerate')) {
                        session_regenerate();
                    } else {
                        @session_start();
                        @session_regenerate_id(true);
                    }
                    // Set session in controller (keep session handling at controller level)
                    session_set('user_id', $user['id']);
                    session_set('login', $user['login']);
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
                    // set flash message for login page
                    session_set('registration_success', "Rejestracja zakończona sukcesem. Możesz się teraz zalogować.");
                    return $this->redirect('/login');
                } elseif (is_array($result) && isset($result['success']) && isset($result['warning'])){
                    // Registration succeeded but with a profile upload warning — expose via session for UI
                    session_set('profile_upload_warning', $result['warning']);
                    session_set('registration_success', "Rejestracja zakończona sukcesem. Możesz się teraz zalogować.");
                    return $this->redirect('/login');
                } else {
                    $data['error'] = is_array($result) ? implode(' ', $result) : $result;
                }
                
            }
            return $this->render('register_view', $data);
        }

        public function logout(){
            // Clear session here and DO NOT preserve cart/quantities (empty cart on logout)
            session_restart_preserve([]);
            // ensure a new session id / cookie after logout
            if (function_exists('session_regenerate')) {
                session_regenerate();
            } else {
                @session_start();
                @session_regenerate_id(true);
            }
            return $this->redirect('/gallery');
        }
    }