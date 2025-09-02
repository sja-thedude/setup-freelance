<?php

namespace App\Utils;

use App\Utils\Common;

/**
 * Class Image
 * @package App\Services
 */
class Image extends Common {
    /**
     * Create a class instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Convert Image To Base64.          
     *
     * @return array('success' => true, 'msg' => 'message', 'data' => 'base64')
     */
    public function convertImageToBase64($path)
    {
        if(empty($this->existFile($path))) {
            return array('success' => false, 'msg' => 'image_file_not_found');
        }

        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        
        return array('success' => true, 'data' => $base64);
    }

    /**
     * Convert Base64 To Image.          
     *
     * @return array('success' => true, 'msg' => 'message', 'data' => 'image.png')
     */
    public function convertBase64ToImage($data, $destDir)
    {
        if(empty($data)){
            return array('success' => false, 'msg' => 'image_base64_required');
        }

        if(empty($destDir)){
            return array('success' => false, 'msg' => 'image_destination_directory_not_null');
        }

        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, TRUE);
        }

        $pngUrl = rand(0, 1000) . time() .".png";
        $path = $destDir . '/' . $pngUrl;

        \Image::make(file_get_contents($data))->save($path);

        return array('success' => true, 'data' => $pngUrl);
    }
}
