<?php

namespace jonnybo\FileStorage;

use Yii;
use yii\db\Exception;

class FileHelper
{

    public static function curl($url, $headers = false, $filename = false) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if ($headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        if ($filename) {
            curl_setopt($curl, CURLOPT_HEADER, 0);
            $fp = fopen($filename, 'w+');
            curl_setopt($curl, CURLOPT_FILE, $fp);
        }
        $out = curl_exec($curl);
        clearstatcache();
        if (file_exists($filename) && filesize($filename) > 0) {
            fclose($fp);
        }
        curl_close($curl);
        if ($filename)
            @fclose($fp);
        return $out;
    }

    public static function getPath($dir = '', $useHourDir = true) {
        $path = Yii::$app->basePath . '/web/files';
        $hourdir = '';
        if ($dir !== '')
            $path = $path . '/' . $dir;
        self::createDir($path);
        if ($useHourDir) {
            $hourdir = '/' . date('Y-m-d_H');
            self::createDir($path . $hourdir);
        }
        return $path . $hourdir;
    }

    public static function createDir($dir) {
        if (!file_exists($dir))
            mkdir($dir, 0777);
    }

    public static function getFileContent($url) {
        if ($content = file_get_contents($url)) {
            return $content;
        }
        return false;
    }

    public static function getFile($file) {
        if (file_exists($file) && filesize($file) > 0) {
            return $file;
        }
        return false;
    }

    public static function getFileCurl($url, $header, $file) {
        self::curl($url, $header, $file);
        if ($result = self::getFile($file)) {
            return $result;
        }
        return false;
    }

    public static function getUploadFileName($files, $keepfilename = false) {
        if ($keepfilename)
            $filename = $files->baseName . '.' . $files->extension;
        else
            $filename = md5_file($files->tempName) . '_' . $files->baseName . '.' . $files->extension;
        return $filename;
    }

    public static function getSaveFileName($file, $filename, $keepfilename = false) {
        if (!$keepfilename) {
            $filename = md5_file($file) . '_' . basename($filename);
        }
        return $filename;
    }

    public static function getClearFileName($filename) {
        return preg_replace('/[^\w\-\._]/u','', $filename);
    }

    public static function processResult($result) {
        if (isset($result['error']) && $result['error'])
            throw new Exception($result['error']);
        if (isset($result['success']) && $result['success'])
            return $result['success'];
    }

}