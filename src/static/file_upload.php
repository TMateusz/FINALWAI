<?php
    class FileUploader{
        public $maxSize = 1024*1024; // 1 MB
        public $extensions = ['jpg', 'png'];
        public $errors=[];

        public function upload($fieldName, $destinationDir, $customName = null){
            if (!isset($_FILES[$fieldName])) {
                $this->errors[] = 'Nie przesłano pliku w polu ' . $fieldName . '.';
                return false;
            }

            $file = $_FILES[$fieldName];

            // normalize destination dir and ensure it exists
            $destinationDir = rtrim($destinationDir, '/\\') . DIRECTORY_SEPARATOR;
            if (!is_dir($destinationDir)){
                if (!@mkdir($destinationDir, 0777, true)){
                    $this->errors[] = 'Nie można utworzyć katalogu docelowego: ' . $destinationDir;
                    return false;
                }
            }

            if ($customName !== null) {
                $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $targetFile = $destinationDir . $customName . '.' . $fileType;
            } else {
                $targetFile = $destinationDir . basename($file['name']);
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            }

            if ($file['size'] > $this->maxSize) {
                $this->errors[] = 'Plik jest za duży. Maksymalny rozmiar to 1 MB.';
                return false;
            }

            if (!in_array($fileType, $this->extensions)) {
                $this->errors[] = 'Nieobsługiwany format pliku. Dozwolone: jpg, png.';
                return false;
            }

            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                return basename($targetFile);
            }

            $this->errors[] = 'Błąd przesyłania pliku.';
            return false;
        }

        // POPRAWA
        public function uploadFile(string $fieldName, string $destinationDir, ?string $customName = null){
            return $this->upload($fieldName, $destinationDir, $customName);
        }

    public function getErrors(): array {
        return $this->errors;
    }
}
