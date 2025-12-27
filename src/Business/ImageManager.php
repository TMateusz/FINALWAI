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

            // Call the project helper directly (assumed available)
            return createThumbnail($source, $destination, $maxW, $maxH);

            // Basic fallback: load image and resample
            $info = getimagesize($source);
            if (!$info) return false;
            $type = $info[2];
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $img = imagecreatefromjpeg($source);
                    break;
                case IMAGETYPE_PNG:
                    $img = imagecreatefrompng($source);
                    break;
                case IMAGETYPE_GIF:
                    $img = imagecreatefromgif($source);
                    break;
                default:
                    return false;
            }

            $width = imagesx($img);
            $height = imagesy($img);
            $thumb = imagecreatetruecolor($maxW, $maxH);
            if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
                imagecolortransparent($thumb, imagecolorallocate($thumb, 0, 0, 0));
                if ($type == IMAGETYPE_PNG) {
                    imagealphablending($thumb, false);
                    imagesavealpha($thumb, true);
                }
            }
            imagecopyresampled($thumb, $img, 0,0,0,0, $maxW, $maxH, $width, $height);
            $saved = false;
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $saved = imagejpeg($thumb, $destination, 85);
                    break;
                case IMAGETYPE_PNG:
                    $saved = imagepng($thumb, $destination, 6);
                    break;
                case IMAGETYPE_GIF:
                    $saved = imagegif($thumb, $destination);
                    break;
            }
            imagedestroy($img);
            imagedestroy($thumb);
            return $saved;
        }
        
        public function searchImages($qurty, $login=null){
            $images = $this->imageModel->searchByTitle($qurty, $login);
            return $this->prepareMiniature($images);
        }
        
        public function uploadImage($title, $author, $fileArray, $isPublic){
            // use uploader from project-root `static/` (not src/static)
            require __DIR__ . '/../../static/file_upload.php';
            $uploader = new FileUploader();
            $uploadPath = __DIR__ . '/../../static/images/';
            $savedFilename = $uploader->uploadFromArray($fileArray, $uploadPath);

            if (!$savedFilename) {
                return $uploader->getErrors();
            }

            // create miniature with _miniature suffix (filesystem under project root static/)
            $source = __DIR__ . '/../../static/images/' . $savedFilename;
            $miniName = preg_replace('/(\.[^.]+)$/', '_miniature$1', $savedFilename);
            $miniDestDir = __DIR__ . '/../../static/miniature/';
            if (!is_dir($miniDestDir)) mkdir($miniDestDir, 0777, true);
            $miniDest = $miniDestDir . $miniName;
            $this->createMiniature($source, $miniDest);

            // Save DB record (store basename only)
            $success = $this->imageModel->saveImage($title, $author, $savedFilename, $isPublic);
            return $success;
        }

        /**
         * Upload or replace a user's profile image. The saved filename will be the user's login + extension.
         */
        public function uploadProfileImage(string $login, array $file){
            // Create only a miniature from the uploaded tmp file — do not save the full-size image.
            $allowed = ['jpg','png'];
            $maxSize = 1024 * 1024; // 1MB

            $origName = $file['name'] ?? '';
            $tmpName = $file['tmp_name'] ?? null;
            $size = $file['size'] ?? 0;

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

            // createMiniature accepts a filesystem source; pass the uploaded temp file
            $this->createMiniature($tmpName, $miniDest);

            // Return the miniature filename
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
