<?php
    require_once __DIR__ . '/../../static/miniature.php';

    class ImageManager{
        private $imageModel;

        public function __construct($imageModel = null){
            $this->imageModel = $imageModel ?? new ImageModel();
        }

        public function getAllImages($login=null){
            $images = $this->imageModel->getAllImages($login);
            return $this->prepareMiniature($images);
        }
        public function prepareMiniature(&$images){
            require_once __DIR__ . '/../../static/miniature.php';

            $result=[];
            $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            $baseUrl = ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') ? '' : $scriptDir;
            foreach ($images as $image){
                $source = __DIR__ . '/../../static/images/' . $image['plik'];
                $destination = __DIR__ . '/../../static/miniature/' .  preg_replace('/(\.[^.]+)$/', '_miniature$1', $image['plik']);

                if (file_exists($source)){
                    if (!file_exists($destination)){
                        $this->createMiniature($source, $destination);
                    }
                }

                $image['miniature'] = $baseUrl . '/static/miniature/' . basename($destination);
                $image['path'] = $baseUrl . '/static/images/' . $image['plik'];
                $result[] = $image;
            }
            return $result;
        }

        public function createMiniature(string $source, string $destination, int $maxW = 200, int $maxH = 125){
            $dir = dirname($destination);
            if (!is_dir($dir)){
                mkdir($dir, 0777, true);
            }
            return createThumbnail($source, $destination, $maxW, $maxH);
        }
        
        public function searchImages($qurty, $login=null){
            $images = $this->imageModel->searchByTitle($qurty, $login);
            return $this->prepareMiniature($images);
        }
        
        public function uploadImage($title, $author, $fileArray, $isPublic){
            require __DIR__ . '/../../static/file_upload.php';
            $uploader = new FileUploader();
            $uploadPath = __DIR__ . '/../../static/images/';
            $savedFilename = $uploader->uploadFromArray($fileArray, $uploadPath);

            if (!$savedFilename) {
                return $uploader->getErrors();
            }

            // prefix maker
            $source = __DIR__ . '/../../static/images/' . $savedFilename;
            $miniName = preg_replace('/(\.[^.]+)$/', '_miniature$1', $savedFilename);
            $miniDestDir = __DIR__ . '/../../static/miniature/';
            if (!is_dir($miniDestDir)) mkdir($miniDestDir, 0777, true);
            $miniDest = $miniDestDir . $miniName;
            $this->createMiniature($source, $miniDest);

            // save db
            $success = $this->imageModel->saveImage($title, $author, $savedFilename, $isPublic);
            return $success;
        }

        
        public function uploadProfileImage(string $login, array $file){
            $allowed = ['jpg','png'];
            $maxSize = 1024 * 1024;

            $origName = $file['name'];
            $tmpName = $file['tmp_name'];
            $size = $file['size'];

            $errors = [];

            if (!$tmpName || !is_uploaded_file($tmpName)){
                $errors[] = 'Nie znaleziono przesłanego pliku.';
            }

            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)){
                $errors[] = 'Nieobsługiwany format pliku. Dozwolone: jpg, png.';
            }
            if ($size > $maxSize){
                $errors[] = 'Plik jest za duży. Maksymalny rozmiar to 1 MB.';
            }

            if (!empty($errors)) {
                return $errors;
            }

            $safeLogin = preg_replace('/[^a-zA-Z0-9_-]/', '_', $login);
            $miniDir = __DIR__ . '/../../static/profileimages/';

            $miniName = $safeLogin . '.' . $ext;
            $miniDest = $miniDir . $miniName;

            $this->createMiniature($tmpName, $miniDest);

            return $miniName;
        }

        public function getCart(array $cart, array $quantities = [], $login = null){
            if (empty($cart)){
                return [];
            }
            $allImages = $this->imageModel->getAllImages($login);
            $cartImages = [];
            foreach ($allImages as $image){
                if (in_array($image['plik'], $cart)){
                    $image['quantity'] = $quantities[$image['plik']] ?? 1;
                    $cartImages[] = $image;
                }
            }
            return $this->prepareMiniature($cartImages);
        }
    }
