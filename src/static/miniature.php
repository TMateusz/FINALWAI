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
 * @param $src - a valid file location
 * @param $dest - a valid file target
 * @param $targetWidth - desired output width (default 200)
 * @param $targetHeight - desired output height (default 125)
 */
function createThumbnail($src, $dest, $targetWidth = 200, $targetHeight = 125) {

    $info = @getimagesize($src);
    $type = $info[2] ?? null;

    if (!$type || !isset(IMAGE_HANDLERS[$type])) {
        return null;
    }

    $loadFunc = IMAGE_HANDLERS[$type]['load'];

    if (function_exists($loadFunc)) {
        $image = call_user_func($loadFunc, $src);
        if (!$image) {
            return null;
        }
    } else {
        return null;
    }
    // get original image width and height
    $width = imagesx($image);
    $height = imagesy($image);

    // create duplicate image of exactly 200x125
    $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

    // set transparency options for GIFs and PNGs
    if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {

        // make image transparent
        imagecolortransparent(
            $thumbnail,
            imagecolorallocate($thumbnail, 0, 0, 0)
        );

        // additional settings for PNGs
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }
    }

    // copy entire source image to 200x125 thumbnail
    imagecopyresampled(
        $thumbnail,
        $image,
        0, 0, 0, 0,
        $targetWidth, $targetHeight,
        $width, $height
    );


    // 3. Save the $thumbnail to disk
    // - call the correct save method
    // - set the correct quality level

    // save the duplicate version of the image to disk
    $saveFunc = IMAGE_HANDLERS[$type]['save'];
    $quality = IMAGE_HANDLERS[$type]['quality'] ?? null;
    if (function_exists($saveFunc)) {
        if ($quality !== null) {
            return call_user_func($saveFunc, $thumbnail, $dest, $quality);
        } else {
            return call_user_func($saveFunc, $thumbnail, $dest);
        }
    }

    // As fallback, try to use imagepng/jpeg directly with defaults
    if ($type == IMAGETYPE_JPEG) {
        return imagejpeg($thumbnail, $dest, 85);
    } elseif ($type == IMAGETYPE_PNG) {
        return imagepng($thumbnail, $dest, 6);
    } elseif ($type == IMAGETYPE_GIF) {
        return imagegif($thumbnail, $dest);
    }

    return null;
}
