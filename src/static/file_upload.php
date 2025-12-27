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

            // collect validation errors (size + extension) before returning
            if (($file['size'] ?? 0) > $this->maxSize) {
                $this->errors[] = 'Plik jest za duży. Maksymalny rozmiar to 1 MB.';
            }

            if (!in_array($fileType, $this->extensions)) {
                $this->errors[] = 'Nieobsługiwany format pliku. Dozwolone: jpg, png.';
            }

            if (!empty($this->errors)) {
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

        public function uploadFromArray(array $file, string $destinationDir, ?string $customName = null){
            // Same validation as upload(), but uses provided $file array instead of reading $_FILES
            if (empty($file) || !isset($file['name'])) {
                $this->errors[] = 'Nie przesłano pliku.';
                return false;
            }

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

            // Check upload error first and collect messages
            $error = $file['error'] ?? UPLOAD_ERR_OK;
            error_log('FileUploader::uploadFromArray: upload error=' . $error . ' size=' . ($file['size'] ?? 'null'));
            if ($error !== UPLOAD_ERR_OK) {
                if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                    $this->errors[] = 'Plik jest za duży. Maksymalny rozmiar to 1 MB.';
                } else {
                    $this->errors[] = 'Błąd przesyłania pliku (kod: ' . $error . ').';
                }
                // also check extension from filename to report format issues as well
                $ft = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
                if ($ft && !in_array($ft, $this->extensions)) {
                    $this->errors[] = 'Nieobsługiwany format pliku. Dozwolone: jpg, png.';
                }
                return false;
            }

            if (($file['size'] ?? 0) > $this->maxSize) {
                $this->errors[] = 'Plik jest za duży. Maksymalny rozmiar to 1 MB.';
            }

            if (!in_array($fileType, $this->extensions)) {
                $this->errors[] = 'Nieobsługiwany format pliku. Dozwolone: jpg, png.';
            }

            if (!empty($this->errors)) {
                return false;
            }

            $tmp = $file['tmp_name'] ?? null;
            if ($tmp && is_uploaded_file($tmp)){
                $moved = move_uploaded_file($tmp, $targetFile);
            } else if ($tmp && file_exists($tmp)){
                $moved = rename($tmp, $targetFile) || copy($tmp, $targetFile);
            } else {
                $this->errors[] = 'Brak tymczasowego pliku do przeniesienia.';
                return false;
            }

            if ($moved) {
                return basename($targetFile);
            }

            $this->errors[] = 'Błąd przesyłania pliku.';
            return false;
        }

    public function getErrors(): array {
        return $this->errors;
    }
}
