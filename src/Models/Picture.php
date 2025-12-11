<?php

class Picture {
    private $id;
    private $filename;
    private $title;
    private $author;
    private $isPublic;
    private $uploadDate;
    
    // Ścieżki
    private $imageDir = 'static/images/';
    private $thumbDir = 'static/miniature/';
    
    // Właściwości pliku
    private $size;
    private $extension;
    private $mimeType;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->filename = $data['plik'] ?? null;
            $this->title = $data['tytul'] ?? null;
            $this->author = $data['autor'] ?? null;
            $this->isPublic = $data['publiczny'] ?? 1;
            
            if ($this->filename) {
                $this->loadFileInfo();
            }
        }
    }
    
    private function loadFileInfo() {
        $fullPath = $this->getFullPath();
        
        if (file_exists($fullPath)) {
            $this->size = filesize($fullPath);
            $this->extension = strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
            $this->mimeType = mime_content_type($fullPath);
        }
    }
    
    // Gettery
    public function getId() {
        return $this->id;
    }
    
    public function getFilename() {
        return $this->filename;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getAuthor() {
        return $this->author;
    }
    
    public function isPublic() {
        return $this->isPublic == 1;
    }
    
    public function getUploadDate() {
        return $this->uploadDate;
    }
    
    public function getSize() {
        return $this->size;
    }
    
    public function getSizeFormatted() {
        if ($this->size < 1024) {
            return $this->size . ' B';
        } elseif ($this->size < 1024 * 1024) {
            return round($this->size / 1024, 2) . ' KB';
        } else {
            return round($this->size / (1024 * 1024), 2) . ' MB';
        }
    }
    
    public function getExtension() {
        return $this->extension;
    }
    
    public function getMimeType() {
        return $this->mimeType;
    }
    
    // Ścieżki do plików
    public function getFullPath() {
        return __DIR__ . '/../../' . $this->imageDir . $this->filename;
    }
    
    public function getPath() {
        return $this->imageDir . $this->filename;
    }
    
    public function getThumbPath() {
        $thumbFilename = preg_replace('/(\.[^.]+)$/', '_thumb$1', $this->filename);
        return $this->thumbDir . $thumbFilename;
    }
    
    public function getFullThumbPath() {
        $thumbFilename = preg_replace('/(\.[^.]+)$/', '_thumb$1', $this->filename);
        return __DIR__ . '/../../' . $this->thumbDir . $thumbFilename;
    }
    
    public function getMiniature() {
        return $this->getThumbPath();
    }
    
    // Sprawdzenia
    public function exists() {
        return file_exists($this->getFullPath());
    }
    
    public function thumbnailExists() {
        return file_exists($this->getFullThumbPath());
    }
    
    // Settery
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setFilename($filename) {
        $this->filename = $filename;
        $this->loadFileInfo();
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setAuthor($author) {
        $this->author = $author;
    }
    
    public function setIsPublic($isPublic) {
        $this->isPublic = $isPublic ? 1 : 0;
    }
    
    // Konwersja do tablicy (dla kompatybilności z istniejącym kodem)
    public function toArray() {
        return [
            'id' => $this->id,
            'plik' => $this->filename,
            'tytul' => $this->title,
            'autor' => $this->author,
            'publiczny' => $this->isPublic,
            'data_upload' => $this->uploadDate,
            'path' => $this->getPath(),
            'miniature' => $this->getMiniature(),
            'size' => $this->size,
            'size_formatted' => $this->getSizeFormatted(),
            'extension' => $this->extension,
            'mime_type' => $this->mimeType
        ];
    }
}
