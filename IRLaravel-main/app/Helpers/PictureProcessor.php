<?php

namespace App\Helpers;

use App\Jobs\ProcessPicture;
use Imagick;

/**
 * Class PictureProcessor
 * @package App\Helpers
 */
class PictureProcessor
{
    public $folder; #module
    public $module_id = null;
    public $sizes = array(); //orig | 300x300. Note orig = original image, 300x300 size is custom
    public $path = null;
    public static $types = array(
        'c'  => 'crop',
        's'  => 'scale',
        'sc' => 'scalecrop',
        'sf' => 'scalefit'
    );
    public static $colors = array(
        'c'  => 'color',
        'gs' => 'grayscale'
    );

    /**
     * PictureProcessor constructor.
     * @param $folder
     * @param null $sizes
     * @param null $module_id
     * @param null $path
     */
    public function __construct($folder = null, $sizes = null, $module_id = null, $path = null)
    {
        $this->folder = $folder;
        $this->module_id = $module_id;
        $this->sizes = $sizes;
        $this->path = $path;
    }

    /**
     * @param $folder
     * @param $basePath
     * @return string
     */
    public static function getPublicPath($folder, $basePath = false) {
        if ($basePath) {
            return $basePath . '/' . $folder;
        }

        return '/storage/' . $folder;
    }

    /**
     * @param $folder
     * @param $basePath
     * @return string
     */
    public static function getPath($folder, $basePath = false) {
        if ($basePath) {
            return base_path() . '/public/' . $basePath . '/' . $folder;
        }

        return base_path() . '/public/storage/' . $folder;;
    }

    /**
     * @param $folder
     * @param $size
     * @param $filename
     * @param null $module_id
     * @param string $type
     * @param string $color
     * @param bool $basePath
     * @param bool $placeHolder
     * @return string
     * @throws \ImagickException
     */
    public static function get($folder, $size, $filename, $module_id = null, $type = 'c', $color = 'c', $basePath = false, $placeHolder = false)
    {
        if (!key_exists($type, self::$types)) {
            $type = 'c';
        }
        if (!key_exists($color, self::$colors)) {
            $color = 'c';
        }

        if (!$filename || !is_string($filename)) {
            if (!$placeHolder) {
                return self::get('no-img', $size, 'no_img.png', null, $type, $color);
            }
            elseif($placeHolder === false) {
                return self::get('no-img', $size, 'no_img.png', null, $type, $color, false, $placeHolder);
            }
            else {
                return null;
            }
        }

        $path = self::getPath($folder, $basePath);
        $publicPath = self::getPublicPath($folder, $basePath);

        $pathToOpen = $path . '/' . ($module_id ? $module_id . '/' : '') . $type . '_' . $color . '/' . $size . '/' . $filename;;
        $publicPathToOpen = $publicPath . '/' . ($module_id ? $module_id . '/' : '') . $type . '_' . $color . '/' . $size . '/' . $filename;

        // Return if is original image or desired image does not exist
        if ($size == 'orig' || !file_exists($pathToOpen)) {
            // Process background
            if (
                file_exists($path . '/' . $filename)
                && !file_exists($pathToOpen)
                && $size != 'orig'
            ) {
                dispatch(new ProcessPicture([
                    'path' => $path,
                    'pathToOpen' => $pathToOpen,
                    'publicPathToOpen' => $publicPathToOpen,
                    'filename' => $filename,
                    'size' => $size,
                    'module_id' => $module_id,
                    'type' => $type,
                    'color' => $color
                ]));
            }

            return $publicPath . '/' . $filename;
        }

        // Return if desired image
        if (file_exists($pathToOpen)) {
            return $publicPathToOpen;
        }

        if ($filename !== 'no_img.png') {
            if (!$placeHolder) {
                return self::getImagePath('no-img', $size, 'no_img.png', null, $type, $color);
            }
            else {
                return self::getImagePath('no-img', $size, 'no_img.png', null, $type, $color, false, $placeHolder);
            }
        }

        return '/builds/images/no-img.png';
    }

    /**
     * Get image folder from path
     *
     * @param string|null $path
     * @return string
     */
    public static function getImageFolder($path)
    {
        $folder = "";

        try {
            if ($path) {
                $folder = explode('/', $path);

                if (!empty($folder[0])) {
                    $folder = $folder[0];
                }
            }
        } catch (\Exception $ex) {

        }

        return $folder;
    }

    /**
     * Get image name from path
     *
     * @param string|null $path
     * @return string
     */
    public static function getImageName($path)
    {
        $fileName = "no_img.png";

        try {
            if ($path) {
                $fileName = explode('/', $path);

                if (!empty($fileName[1])) {
                    $fileName = $fileName[1];
                }
            }
        } catch (\Exception $ex) {

        }

        return $fileName;
    }

    /**
     * @param $folder
     * @param $size
     * @param $filename
     * @param null $module_id
     * @param string $type
     * @param string $color
     * @param bool $basePath
     * @param bool $placeHolder
     * @return string
     */
    public static function getImagePath($folder, $size, $filename, $module_id = null, $type = 'c', $color = 'c', $basePath = false, $placeHolder = false)
    {

    }

    /**
     * @param $path
     * @param $pathToOpen
     * @param $publicPathToOpen
     * @param $filename
     * @param $size
     * @param null $module_id
     * @param string $type
     * @param string $color
     * @return mixed
     * @throws \ImagickException
     */
    public static function croppingProcess($path, $pathToOpen, $publicPathToOpen, $filename, $size, $module_id = null, $type = 'c', $color = 'c')
    {
        $origPath = $path . '/' . $filename;
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Image cropping process
        if (!file_exists($pathToOpen) && file_exists($origPath)):
            list($width, $height) = explode('x', $size);

            #create folder
            if (!is_dir($path . '/' . ($module_id ? $module_id . '/' : '') . $type . '_' . $color . '/' . $size)) {
                mkdir($path . '/' . ($module_id ? $module_id . '/' : '') . $type . '_' . $color . '/' . $size, 0777, true);
            }

            $im = new Imagick($origPath);

            #Orientation fix
            self::autoRotateImage($im);

            $im->transformImageColorspace(Imagick::COLORSPACE_SRGB);

            // IMAGE COMPRESSION (JPG|PNG)
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $im->setCompression(Imagick::COMPRESSION_JPEG);
                $im->setImageCompressionQuality(100);
                $im->setImageFormat('jpeg');
            } elseif (in_array($extension, ['png', 'PNG'])) {
                $im->setCompression(Imagick::COMPRESSION_UNDEFINED);
                $im->setImageCompressionQuality(0);
                $im->setImageFormat('png');
            }

            $im->stripImage();

            switch ($type):
                case 'c':#crop
                    $im = self::crop($im, $width, $height);
                    break;
                case 's':#scale
                    $im = self::scale($im, $width, $height);
                    break;
                case 'sc':#scale crop
                    $im = self::scaleCrop($im, $width, $height);
                    break;
                case 'sf':#scale fit
                    $im = self::scaleFit($im, $width, $height);
                    break;
            endswitch;

            if ($color == 'gs'):
                $im->setimagecolorspace(2);
            endif;

            $im->writeImage($pathToOpen);
            $im->clear();

            return $publicPathToOpen;
        endif;
    }

    /**
     * @param Imagick $image
     * @param $width
     * @param $height
     * @return Imagick
     * @throws \ImagickException
     */
    private static function crop(Imagick $image, $width, $height)
    {

        $image->cropThumbnailImage($width, $height);

        return $image;
    }

    /**
     * @param Imagick $image
     * @param $width
     * @param $height
     * @return Imagick
     * @throws \ImagickException
     */
    private static function scale(Imagick $image, $width, $height)
    {
        if ($width == 0 || $height == 0):
            $image->scaleImage($width, $height);
        else:
            $imageHeight = $image->getImageHeight();
            $imageWidth = $image->getImageWidth();

            if ($imageWidth > $width):
                $image->scaleImage($width, $height, true);
            endif;
            if ($imageHeight > $height):
                $image->scaleImage($width, $height, true);
            endif;
        endif;

        return $image;
    }

    /**
     * @param Imagick $image
     * @param $width
     * @param $height
     * @return Imagick
     * @throws \ImagickException
     */
    private static function scaleCrop(Imagick $image, $width, $height)
    {
        $imageHeight = $image->getImageHeight();
        $imageWidth = $image->getImageWidth();

        if ($imageWidth > $width):
            $image->scaleImage($width, $height, true);
        endif;
        if ($imageHeight > $height):
            $image->scaleImage($width, $height, true);
        endif;

        $oldWidth = $image->getImageWidth();
        $oldHeight = $image->getImageHeight();

        #coords to center image inside fixed width/height canvas
        $x = ($width - $oldWidth) / 2;
        $y = ($height - $oldHeight) / 2;
        #create new image with the user image centered
        $newImage = new Imagick();
        $bgColor = ($image->getImageFormat() == 'png') ? 'none' : 'white';
        $newImage->newImage($width, $height, new \ImagickPixel($bgColor));
        $newImage->compositeImage($image, Imagick::COMPOSITE_OVER, $x, $y);

        return $newImage;
    }

    /**
     * @param Imagick $image
     * @param $width
     * @param $height
     * @return Imagick
     */
    private static function scaleFit(Imagick $image, $width, $height)
    {

        $image->thumbnailimage($width, $height, true);

        return $image;
    }

    /**
     * @param Imagick $image
     */
    private static function autoRotateImage(Imagick $image)
    {
        $orientation = $image->getImageOrientation();
        switch ($orientation):
            case Imagick::ORIENTATION_BOTTOMRIGHT :
                $image->rotateimage("#000", 180);
                // rotate 180 degrees
                break;

            case Imagick::ORIENTATION_RIGHTTOP :
                $image->rotateimage("#000", 90);
                // rotate 90 degrees CW
                break;

            case Imagick::ORIENTATION_LEFTBOTTOM :
                $image->rotateimage("#000", -90);
                // rotate 90 degrees CCW
                break;
        endswitch;
        // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
        $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
    }
}
