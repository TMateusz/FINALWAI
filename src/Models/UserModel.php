<?php
    class UserModel{
        
        public function getByLogin($login){
            $db=Database::getUserDB();
            $query = $db->prepare("SELECT id, login, password, email FROM users WHERE login = ?");
            $query->execute([$login]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result ? $result : null;
        }

        public function create($login, $password, $email){
            $db = Database::getUserDB();
            $query=$db->prepare("SELECT id FROM users WHERE login = ? OR email = ?");
            $query->execute([$login, $email]);
            $result = $query->fetch();
            if ($result) {
                return false; 
            }
            $query = $db->prepare("INSERT INTO users (login, password, email) VALUES (?, ?, ?)");
            return $query->execute([$login, $password, $email]);
        }
    }