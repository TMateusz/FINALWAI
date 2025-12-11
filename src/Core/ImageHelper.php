<?php

const IMAGE_HANDLERS = [
    IMAGETYPE_JPEG => [
        'load' => 'imagecreatefromjpeg',
        'save' => 'imagejpeg',
        'quality' => 85
    ],
    IMAGETYPE_PNG => [
        'load' => 'imagecreatefrompng',
        'save' => 'imagepng',
        'quality' => 6
    ],
    IMAGETYPE_GIF => [
        'load' => 'imagecreatefromgif',
        'save' => 'imagegif'
    ]
];

/**
 * Create a resized thumbnail at exact dimensions (may distort aspect ratio).
 * @param string $src - source file path
 * @param string $dest - destination file path
 * @param int $targetWidth
 * @param int $targetHeight
 * @return bool|null
 */
function createThumbnail($src, $dest, $targetWidth = 200, $targetHeight = 125) {

    if (!file_exists($src)) {
        return null;
    }

    $type = @exif_imagetype($src);

    if (!$type || !isset(IMAGE_HANDLERS[$type])) {
        return null;
    }

    $loader = IMAGE_HANDLERS[$type]['load'];
    $saver = IMAGE_HANDLERS[$type]['save'];
    $quality = IMAGE_HANDLERS[$type]['quality'] ?? null;

    $image = call_user_func($loader, $src);

    if (!$image) {
        return null;
    }

    $width = imagesx($image);
    $height = imagesy($image);

    $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

    if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
        imagecolortransparent(
            $thumbnail,
            imagecolorallocate($thumbnail, 0, 0, 0)
        );

        if ($type == IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }
    }

    imagecopyresampled(
        $thumbnail,
        $image,
        0, 0, 0, 0,
        $targetWidth, $targetHeight,
        $width, $height
    );

    if ($quality !== null) {
        return call_user_func($saver, $thumbnail, $dest, $quality);
    }

    return call_user_func($saver, $thumbnail, $dest);
}
