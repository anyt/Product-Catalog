<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */

/**
 * Folder for storing image files
 */
define('IMAGE_BASE_PATH', APPLICATION_ROOT . '/public/images/');

/**
 * Folder for storing original image files
 */
define('IMAGE_ORIGINAL_PATH', IMAGE_BASE_PATH . 'original/');

/**
 * Folder for storing generated image thumbnails
 */
define('IMAGE_PREVIEW_PATH', IMAGE_BASE_PATH . 'preview/');

/**
 * Image thumbnail width in pixels
 */
define('IMAGE_PREVIEW_WIDTH', 350);

/**
 * Get image info such as mime-type by http url
 *
 * @param string $url
 * @return array
 */
function image_get_info_by_url($url)
{
    static $images = array();
    // if image checked before, get cached info from array
    if (in_array($url, array_keys($images))) {
        return $images[$url];
    }

    // todo find method without @
    $file_info = @getimagesize($url);
    $images[$url] = $file_info;
    return $file_info;
}

/**
 *  Save image from http url to local folder and generate thumbnail
 * @param string $url
 * @return null|string
 */
function image_save($url)
{
    $file_info = image_get_info_by_url($url);

    $filename = md5($url) . '.' . image_extension_by_mime($file_info['mime']);
    $destination = IMAGE_ORIGINAL_PATH . $filename;
    $saved = file_put_contents(
        $destination,
        file_get_contents($url)
    );
    // generate preview
    if ($saved !== false) {
        $preview_path = IMAGE_PREVIEW_PATH . $filename;
        image_generate_preview($destination, $preview_path, $file_info['mime']);
    }
    return ($saved !== false) ? $filename : null;
}

/**
 * Generate image thumbnail
 *
 * @param string $src
 * @param string $dest
 * @param string $mime
 * @return bool
 */
function image_generate_preview($src, $dest, $mime)
{
    /* read the source image */
    switch ($mime) {
        case "image/gif":
            $source_image = imagecreatefromgif($src);
            break;
        case "image/jpeg":
            $source_image = imagecreatefromjpeg($src);
            break;
        case "image/png":
            $source_image = imagecreatefrompng($src);
            break;
        default:
            return false;
    }
    $width = imagesx($source_image);
    $height = imagesy($source_image);

    /* find the "desired height" of this thumbnail, relative to the desired width  */
    $desired_height = floor($height * (IMAGE_PREVIEW_WIDTH / $width));

    /* create a new, "virtual" image */
    $virtual_image = imagecreatetruecolor(IMAGE_PREVIEW_WIDTH, $desired_height);

    /* copy source image at a resized size */
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, IMAGE_PREVIEW_WIDTH, $desired_height, $width, $height);

    /* create the physical thumbnail image to its destination */
    switch ($mime) {
        case "image/gif":
            imagegif($virtual_image, $dest);
            break;
        case "image/jpeg":
            imagejpeg($virtual_image, $dest);
            break;
        case "image/png":
            imagepng($virtual_image, $dest);
            break;
    }
}

/**
 * List of allowed mime-types for image uploads
 *
 * @return array
 */
function image_allowed_mime_types()
{
    return array(
        "image/gif",
        "image/jpeg",
        "image/png"
    );
}

/**
 * Check if image mime-type is allowed for upload
 *
 * @param $mime
 * @return bool
 */
function image_is_allowed_mime_type($mime)
{
    return in_array($mime, image_allowed_mime_types());
}

/**
 * Get file extension by mimetipe
 *
 * @param $mime
 * @return null|string
 */
function image_extension_by_mime($mime)
{
    $extensions = array(
        "image/gif" => 'gif',
        "image/jpeg" => 'jpeg',
        "image/png" => 'png'
    );

    return in_array($mime, array_keys($extensions)) ? $extensions[$mime] : null;
}