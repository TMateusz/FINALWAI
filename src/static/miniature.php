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

    $type = exif_imagetype($src);

    if (!$type || !IMAGE_HANDLERS[$type]) {
        return null;
    }

    $image = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);

    if (!$image) {
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
    return call_user_func(
        IMAGE_HANDLERS[$type]['save'],
        $thumbnail,
        $dest,
        IMAGE_HANDLERS[$type]['quality']
    );
}
