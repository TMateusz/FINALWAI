<?php
    include_once __DIR__. '/../Core/Database.php';
    class ImageModel{
        public static function getAllImages($login=null){
            $db=Database::getGalleryDB();
            if ($login){
                $query = $db->prepare("SELECT id, plik, tytul, autor, publiczny FROM obrazy WHERE publiczny = 1 OR (publiczny = 0 AND autor = ?) ORDER BY id DESC");
                $query->execute([$login]);
            } else {
                $query = $db->prepare("SELECT id, plik, tytul, autor, publiczny FROM obrazy WHERE publiczny = 1 ORDER BY id DESC");
                $query->execute();
            }
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        public static function saveImage($title, $author, $filename, $isPublic){
            $db=Database::getGalleryDB();
            $query = $db->prepare("INSERT INTO obrazy (plik, tytul, autor, publiczny) VALUES (?, ?, ?, ?)");
            return $query->execute([$filename, $title, $author, $isPublic]);
        }

        public static function searchByTitle($title, $login=null){
            $db=Database::getGalleryDB();
            $likeTitle = '%' . $title . '%';
            if ($login){
                $query = $db->prepare("SELECT id, plik, tytul, autor, publiczny FROM obrazy WHERE tytul LIKE ? AND (publiczny = 1 OR (publiczny = 0 AND autor = ?)) ORDER BY id DESC");
                $query->execute([$likeTitle, $login]);
            } else {
                $query = $db->prepare("SELECT id, plik, tytul, autor, publiczny FROM obrazy WHERE tytul LIKE ? AND publiczny = 1 ORDER BY id DESC");
                $query->execute([$likeTitle]);
            }
            // Use PDO fetchAll instead of mysqli get_result/fetch_assoc
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
    }