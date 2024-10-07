<?php
namespace jonnybo\FileStorage\archive;

class ArchiveRar
{

    public function extract($filename, $from, $to) {
        $path_info = pathinfo($filename);
        if ($path_info['extension'] !== 'rar') {
            throw new \Exception(Translate::getTranslate('Неверное расширение файла'));
        }
        if (!file_exists($to)) {
            mkdir($to, '0777', true);
        }
        if ($archive = \RarArchive::open($from . $filename)) {
            if ($entries = $archive->getEntries()) {
                foreach ($entries as $entry) {
                    $entry->extract($to);
                }
                $archive->close();
                return $this->getBaseFiles($to);
            } else {
                throw new \Exception(Translate::getTranslate('Ошибка чтения содержимого архива'));
            }
        } else {
            throw new \Exception(Translate::getTranslate('Невозможно открыть архив'));
        }
    }

    protected function getBaseFiles($path) {
        $dir = opendir($path);
        $dataFiles = [];
        while ($file = readdir($dir)) {
            if ($file <> "." && $file <> "..") {
                if (preg_match("/.dbf$/i", $file)) {
                    $dataFiles[] = $file;
                }
            }
        }
        closedir($dir);
        return $dataFiles;
    }

    public static function unzip($fileaddr, $path)
    {
        if ($archive = \RarArchive::open($fileaddr)) {
            if ($entries = $archive->getEntries()) {
                foreach ($entries as $entry) {
                    $entry->extract($path);
                }
                $archive->close();
                File::setFileRules($path);
            } else {
                throw new \Exception(Translate::getTranslate('Ошибка чтения содержимого архива'));
            }
        } else {
            throw new \Exception(Translate::getTranslate('Невозможно открыть архив'));
        }
        return ['path' => $path, 'files' => File::getFilesInPath($path)];
    }

}