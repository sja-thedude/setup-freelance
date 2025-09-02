<?php

namespace App\Utils;

use File;
use ZipArchive;
use Validator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Class Common
 * @package App\Services
 */
class Common {

    public function __construct() {
        
    }

    /**
     * Remove directory.          
     *
     * @return boolean
     */
    public function removeDirectory($path) {
        if (!is_dir($path)) {
            return false;
        }

        $objects = scandir($path);

        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($path . "/" . $object) == "dir") {
                    $this->removeDirectory($path . "/" . $object);
                } else {
                    $this->removeFile($object, $path);
                }
            }
        }

        reset($objects);
        rmdir($path);

        return true;
    }

    /**
     * Copy directory.          
     *
     * @return $fileName or false
     */
    public function copyDirectory($sourceDir, $destinationDir) {
        if (!is_dir($sourceDir)) {
            return false;
        }

        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0777, TRUE);
        }

        File::copyDirectory($sourceDir, $destinationDir);

        return true;
    }

    /**
     * Upload file.
     *
     * @return $fileName or false
     */
    public function uploadFile($file, $pathUpload, $fileCus = '-') {
        $fileName = time() . $fileCus . $file->getClientOriginalName();
        if (!is_dir($pathUpload)) {
            mkdir($pathUpload, 0777, TRUE);
        }

        if ($file->move($pathUpload, $fileName)) {
            return $fileName;
        }

        return false;
    }

    /**
     * Upload file.
     *
     * @return $fileName or false
     */
    public function uploadFileApi($file, $pathUpload, $fileCus = '-') {
        $fileName = rand(0, 1000) . time() . $fileCus . $file['name'];
        if (!is_dir($pathUpload)) {
            mkdir($pathUpload, 0777, TRUE);
        }
        if (move_uploaded_file($file['tmp_name'], $pathUpload . '/' . $fileName)) {
            return $fileName;
        }
        return false;
    }

    /**
     * Rename file.          
     *
     * @return boolean
     */
    public function renameFile($oldFile, $newFile) {
        if (!File::exists($oldFile)) {
            return false;
        }

        rename($oldFile, $newFile);

        return true;
    }

    /**
     * Remove file.          
     *
     * @return boolean
     */
    public function removeFile($file, $pathUpload) {
        if (!empty($file)) {
            $filePath = $pathUpload . '/' . $file;

            if (!File::exists($filePath)) {
                return false;
            }

            File::delete($filePath);
        }

        return true;
    }

    /**
     * Copy file.          
     *
     * @return $fileName or false
     */
    public function copyFile($fileRoot, $destDir, $destFile) {
        if (!File::exists($fileRoot)) {
            return false;
        }

        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, TRUE);
        }

        File::copy($fileRoot, $destDir . '/' . $destFile);

        return true;
    }

    /**
     * Exist file.          
     *
     * @return boolean
     */
    public function existFile($path) {
        if (!File::exists($path)) {
            return false;
        }

        return true;
    }

    /**
     * Zip file.          
     *
     * @return boolean
     */
    public function zipFile($zipFileName = '', $pathUpload = '', $files = array()) {
        if (empty($zipFileName) || empty($pathUpload) || empty($files)) {
            return false;
        }

        if (!is_dir($pathUpload)) {
            mkdir($pathUpload, 0777, TRUE);
        }

        $zip = new ZipArchive;
        $zipFile = $pathUpload . '/' . $zipFileName;

        if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
            return false;
        }

        foreach ($files as $file) {
            $checkFile = $file['file_path'] . '/' . $file['file_name'];

            if (!file_exists($checkFile)) {
                return false;
            }

            $zip->addFile($checkFile, $file['file_name']);
        }

        $zip->close();

        return true;
    }

    /**
     * Zip folder.          
     *
     * @return boolean
     */
    public function zipFolder($zipFileName = '', $pathUpload = '', $folderPath = '') {
        if (empty($zipFileName) || empty($pathUpload) || empty($folderPath)) {
            return false;
        }

        // Get real path for our folder
        $rootPath = realpath($folderPath);

        // Initialize archive object
        $zip = new ZipArchive();
        $zipFile = $pathUpload . '/' . $zipFileName;
        $zip->open($zipFile, ZipArchive::CREATE || ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();

        return true;
    }

    /**
     * Zip file.          
     *
     * @return boolean
     */
    public function zipExtractTo($zipFileName, $extractPath) {
        if (empty($zipFileName) || empty($extractPath)) {
            return false;
        }

        $checkExist = $this->existFile($zipFileName);

        if (empty($checkExist)) {
            return false;
        }

        $zip = new ZipArchive;
        $res = $zip->open($zipFileName);

        if ($res === true) {
            // extract it to the path we determined above
            $zip->extractTo($extractPath);
            $zip->close();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Upload file.          
     *
     * @return $fileName or false
     */
    public function generateRandomString() {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $characters_number = '0123456789';
        $characters_uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersLength = strlen($characters);
        $charactersNumberLength = strlen($characters_number);
        $charactersUppercaseLength = strlen($characters_uppercase);

        $randomString = '';
        for ($i = 0; $i < 4; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        for ($i = 0; $i < 2; $i++) {
            $randomString .= $characters_uppercase[rand(0, $charactersUppercaseLength - 1)];
        }
        for ($i = 0; $i < 2; $i++) {
            $randomString .= $characters_number[rand(0, $charactersNumberLength - 1)];
        }
        return $randomString;
    }

    /**
     * Upload file.          
     *
     * @return $fileName or false
     */
    public function generateCodeByPrefix($prefix = 'PR') {
        $characters_number = '0123456789';
        $characters_uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersNumberLength = strlen($characters_number);
        $charactersUppercaseLength = strlen($characters_uppercase);

        $randomString = $prefix . '_';

        for ($i = 0; $i < 5; $i++) {
            $randomString .= $characters_uppercase[rand(0, $charactersUppercaseLength - 1)];
        }
        for ($i = 0; $i < 5; $i++) {
            $randomString .= $characters_number[rand(0, $charactersNumberLength - 1)];
        }
        return $randomString;
    }

    /**
     * Make validate.          
     *
     * @return boolean
     */
    public function makeValidate($prefix, $input, $validate) {
        $errors = array();
        $validator = Validator::make($input, $validate);

        if (!$validator->passes()) {
            foreach ($validator->errors()->getMessages() as $key => $error) {
                $errors[$key] = str_slug($prefix . ' ' . $error[0], '_');
            }
        }

        return $errors;
    }

    /**
     * Function : Download a remote file at a given URL and save it to a local folder.
     * Input :
     * $url - URL of the remote file
     * $destDir - Directory where the remote file has to be saved once downloaded.
     * $withName - The name of file to be saved as.
     * Output : 
     * true - if success
     * false - if failed
     * 
     * Note : This function does not work in the Codelet due to network restrictions
     * but does work when executed from command line or from within a webserver.
     */
    public function downloadFile($url, $destDir, $withName) {
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, TRUE);
        }

        try {

            // open file in rb mode
            if ($fpRemote = fopen($url, 'rb')) {

                // local filename
                $local_file = $destDir . "/" . $withName;

                // read buffer, open in wb mode for writing
                if ($fp_local = fopen($local_file, 'wb')) {

                    // read the file, buffer size 8k
                    while ($buffer = fread($fpRemote, 8192)) {

                        // write buffer in  local file
                        fwrite($fp_local, $buffer);
                    }

                    // close local
                    fclose($fp_local);
                } else {
                    // could not open the local URL
                    fclose($fpRemote);
                    return false;
                }

                // close remote
                fclose($fpRemote);

                return true;
            } else {
                // could not open the remote URL
                return false;
            }
        } catch (\Exception $exc) {
            //echo $exc->getTraceAsString();
            return false;
        }
    }

    public function getStringBetween($string, $start, $end) {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0)
            return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    public function guidv4($data) {
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

}
