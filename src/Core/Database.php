<?php

    class Database{
        private static $galleryConnection=null;
        private static $userConnection=null;
        private $host = 'localhost';
        private $user = 'root';

        public static function getGalleryDB(){
            if (self::$galleryConnection==null){
                self::$galleryConnection=new PDO('mysql:host=localhost;dbname=gallery;charset=utf8', 'root', '');
                self::$galleryConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return self::$galleryConnection;
        }

        public static function getUserDB(){
            if (self::$userConnection==null){
                self::$userConnection=new PDO('mysql:host=localhost;dbname=users;charset=utf8', 'root', '');
                self::$userConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return self::$userConnection;
        }
    }