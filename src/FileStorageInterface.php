<?php

namespace jonnybo\FileStorage;

interface FileStorageInterface {

    /**
     * Проверка существования.
     * @return string
     */
    //public function exist();

    /**
     * Сохранение контента.
     * @return string
     */
    public function saveContent($object, $content);

    /**
     * Сохранение файла.
     * @return string
     */
    public function saveFile($object, $file);

    /**
     * Сохранение как.
     * @return string
     */
    public function saveAs($object, $url);

    /**
     * Копирование файла.
     * @return string
     */
    public function copy($object_to, $object_from);

    /**
     * Удаление файла.
     * @return string
     */
    public function delete($objects);

    /**
     * Получить имя файла.
     * @return string
     */
    public function getName($object);

    /**
     * Получить URL файла.
     * @return string
     */
    public function getUrl($object);

    public static function unzipFile($object);

    public static function getFilenameWithoutHash($fileaddr);
}