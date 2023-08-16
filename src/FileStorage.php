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
    public function saveFile($object, $file) {
        if (file_put_contents($object, $file) === false) {
            return false;
        }
        return $object;
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
    public function delete($objects) {
        if (is_array($objects) && !empty($objects)) {
            foreach ($objects as $object) {
                @unlink($object);
            }
        } else {
            @unlink($objects);
        }
    }

    /**
     * Получить имя файла.
     * @return string
     */
    public function getName($object) {
        return basename($object);
    }

    /**
     * Получить URL файла.
     * @return string
     */
    public function getUrl($object) {
        return str_replace(Yii::$app->basePath . '/web/files', '', $object);
    }
}