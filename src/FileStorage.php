<?php

namespace jonnybo\FileStorage;

use Yii;

class FileStorage implements FileStorageInterface
{
    /**
     * Проверка существования.
     * @return string
     */
    public function exist() {

    }

    /**
     * Сохранение контента.
     * @return string
     */
    public function saveContent($object, $content) {
        if (file_put_contents($object, $content) === false) {
            return false;
        }
        return $object;
    }

    /**
     * Сохранение файла.
     * @return string
     */
    public function saveFile($object, $file, $header = []) {
        if ($content = FileHelper::getFileContent($file)) {
            return $this->saveContent($object, $content);
        }
        if ($result = $this->copy($file, $object)) {
            return $result;
        }
        if ($result = FileHelper::getFileCurl($file, $header, $object)) {
            return $result;
        }
        return false;
    }

    /**
     * Сохранение как.
     * @return string
     */
    public function saveAs($object, $url) {

    }

    /**
     * Копирование файла.
     * @return string
     */
    public function copy($object_to, $object_from) {
        if (copy($object_from, $object_to) === false) {
            return false;
        }
        return $object_to;
    }

    /**
     * Удаление файла.
     * @return string
     */
    public function delete($files) {
        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                @unlink($file);
            }
        } else {
            @unlink($files);
        }
    }

    /**
     * Получить имя файла.
     * @return string
     */
    public function getName($file, $filename = false) {
        $filename = md5_file($file) . '_' . (($filename) ? $filename : basename($file));
        return $filename;
    }

    /**
     * Получить URL файла.
     * @return string
     */
    public function getUrl($file) {
        return str_replace(Yii::$app->basePath . '/web/files', '', $file);
    }

    

    public function setFileRule($file) {
        chmod($file, 0666);
    }

    public function setDirRule($path) {
        chmod($path, 0777);
    }
    
    

}