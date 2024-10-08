<?php

namespace jonnybo\FileStorage;

use Yii;

class CloudStorage implements FileStorageInterface
{

    public $ObjectClient;

    public function __construct($config)
    {
        \Minio\Autoloader::register();
        //$this->ObjectClient = new \Minio\Object\ObjectClient($config);
    }

    public function init($config) {
        $this->ObjectClient = new \Minio\Object\ObjectClient($config);
    }

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
        $save_path = $this->ObjectClient->putObjectByContent($object, $content);
        if ($save_path === false) {
            return $this->echo_error_info();
        }
        return $save_path;
    }

    /**
     * Сохранение файла.
     * @return string
     */
    public function saveFile($object, $file) {
        $save_path = $this->ObjectClient->putObjectBySavePath($file, $object);
        if ($save_path === false) {
            return $this->echo_error_info();
        }
        return $save_path;
    }

    /**
     * Сохранение как.
     * @return string
     */
    public function saveAs($object, $url) {
        $result = $this->ObjectClient->getObjectSaveAs($object, $url);
        if ($result === false) {
            return $this->echo_error_info();
        }
        return $result;
    }

    /**
     * Копирование файла.
     * @return string
     */
    public function copy($object_to, $object_from) {
        $targetStorageSavePath = $this->ObjectClient->copyObject($object_from, $object_to);
        if ($targetStorageSavePath === false) {
            return $this->echo_error_info();
        }
        return $targetStorageSavePath;
    }

    /**
     * Удаление файла.
     * @return string
     */
    public function delete($objects) {
        $result = $this->ObjectClient->removeObject($objects);
        if ($result === false) {
            return $this->echo_error_info();
        }
    }

    /**
     * Получить имя файла.
     * @return string
     */
    public function getName($object) {
        $result = $this->ObjectClient->getObject($object);
        if ($result === false) {
            return $this->echo_error_info();
        }
        return $result;
    }

    /**
     * Получить URL файла.
     * @return string
     */
    public function getUrl($object) {
        $url = $this->ObjectClient->getObjectUrl($object, time() + 60);
        if ($url === false) {
            return $this->echo_error_info();
        }
        return $url;
    }

    public function echo_error_info()
    {
        $result = 'error_info: '.$this->ObjectClient->getErrorInfo() . PHP_EOL;
        $result .= 'error_code: '.$this->ObjectClient->getErrorCode() . PHP_EOL;
        $result .= 'error_message: '.$this->ObjectClient->getErrorMessage() . PHP_EOL;
        return $result;
    }


    //сделать методы
    public static function unzipFile($object) {

    }

    public static function getFilenameWithoutHash($fileaddr) {

    }

}