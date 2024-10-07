<?php

namespace jonnybo\FileStorage\archive;

class ArchiveZip
{

    public static function getArchive($files, $fileurl = false, $deleteFiles = false) {
        // Оставлю для совместимости
        self::getArchiveFiles($files, $fileurl, $deleteFiles);
    }

    public static function getArchiveFiles($files, $fileurl = false, $deleteFiles = false) {
        setlocale(LC_ALL, 'en_US.UTF-8');
        $z = new \ZipArchive;
        $dir = Yii::$app->basePath . '/web/files/';
        $dirArchive = Yii::$app->basePath . '/web/files/archive/';
        if (!$fileurl)
            $fileurl = md5(uniqid()) . '.zip';
        $fileurl = Yii::$app->sitefunctions->getSafeFilename($fileurl);
        if($z->open($dirArchive . $fileurl, \ZIPARCHIVE::CREATE) === true){
            foreach ($files as $file) {
                if (file_exists($dir . $file['fileurl'])) {
                    $filename = basename($file['fileurl']);
                    if (isset($file['filename']))
                        $filename = $file['filename'];
                    $filename = Yii::$app->sitefunctions->getSafeFilename($filename);
                    $z->addFile($dir . $file['fileurl'], $filename);
                    if ($deleteFiles) {
                        unlink($dir . $file['fileurl']);
                    }
                } else {
                    //не найден файл
                }
            }
            $z->close();
            //Yii::info('запись в архив - ' . microtime(true), 'dev_log');
            chmod($dirArchive . $fileurl, 0666);
            return 'archive/' . $fileurl;
        } else {
            throw new \Exception(Translate::getTranslate('Ошибка создания архива'));
        }
    }

    protected function removeDir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir")
                        self::removeDir($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function getArchiveDir($dir, $fileurl = false, $delete = false) {
        if (!$fileurl)
            $fileurl = md5(uniqid()) . '.zip';
        $out = self::zipData($dir, Yii::$app->basePath . '/web/files/' . $fileurl);
        if ($out && $delete)
            self::removeDir($dir);
        return $out;
    }

    protected function zipData($source, $destination) {
        setlocale(LC_ALL, 'en_US.UTF-8');
        if (extension_loaded('zip')) {
            if (file_exists($source)) {
                $zip = new \ZipArchive();
                if ($zip->open($destination, \ZIPARCHIVE::CREATE)) {
                    $source = realpath($source);
                    if (is_dir($source)) {
                        $iterator = new \RecursiveDirectoryIterator($source);
                        // skip dot files while iterating
                        $iterator->setFlags(\RecursiveDirectoryIterator::SKIP_DOTS);
                        $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);
                        foreach ($files as $file) {
                            $file = realpath($file);
                            if (is_dir($file)) {
                                $zip->addEmptyDir($files->getSubIterator()->getSubPathname());
                            } else if (is_file($file)) {
                                $zip->addFromString($files->getSubIterator()->getSubPathname(), file_get_contents($file));
                            }
                        }
                    } else if (is_file($source)) {
                        $zip->addFromString(basename($source), file_get_contents($source));
                    }
                }
                return $zip->close();
            }
        }
        return false;
    }

    public static function zip($source, $destination) {
        // Оставлю до совместимости
        return self::zipData($source, $destination);
    }

    public static function unzip($fileaddr, $path)
    {
        setlocale(LC_ALL, 'en_US.UTF-8');
        $zip = new \ZipArchive;

        if ($zip->open($fileaddr) === TRUE) {
            /*for($i = 0; $i < $zip->numFiles; $i++)
            {
                $filename = iconv('CP866', 'UTF-8',$zip->getNameIndex($i, 64));
                $zip->renameIndex($i, $filename);
            }
            $zip->close();

            $zip->open($fileaddr);*/
            $zip->extractTo($path.'/');
            $zip->close();

            File::setFileRules($path);
        } else
            throw new \Exception(str_replace('$file$', $fileaddr, Translate::getTranslate('Не удается разархивировать $file$')));

        return ['path' => $path, 'files' => File::getFilesInPath($path)];
    }

}