<?php

namespace jonnybo\FileStorage;

use Yii;

class FileStorage implements FileStorageInterface
{
    /**
     * Проверка существования.
     * @return string
     */
    public function exist($path, $filename, $_base_path = null) {
        if (is_null($_base_path)) {
            $_base_path = '';
        } else {
            $_base_path .= basename($path) . '/';
        }
        $out = false;
        foreach (glob($path . '/*') as $file) {
            if (is_dir($file)) {
                $out = $this->exist($file, $filename, $_base_path);
                if ($out)
                    return $out;
            } else {
                if (strpos(basename($file), $filename) !== false) {
                    return $_base_path . basename($file);
                }
            }
        }
        return $out;
    }

    /**
     * Сохранение контента.
     * @return array
     */
    public function saveContent($object, $content) {
        try {
            if (file_put_contents($object, $content) === false) {
                return ['error' => 'Ошибка сохранения контента в файл!'];
            }
            return ['success' => $object];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Сохранение файла.
     * @return array
     */
    public function saveFile($filename, $file, $dir = '', $useHourDir = true, $header = [], $keepFileName = false) {
        try {
            $object = tempnam(sys_get_temp_dir(), 'Myls');
            $result = false;
            if (!empty($header)) {
                $result = FileHelper::getFileCurl($file, $header, $object);
            } else {
                if ($content = FileHelper::getFileContent($file)) {
                    $result = FileHelper::processResult($this->saveContent($object, $content));
                }
                if (!$result) {
                    $result = FileHelper::processResult($this->copy($file, $object));
                }
                if (!$result) {
                    $result = FileHelper::getFileCurl($file, $header, $object);
                }
            }
            if ($result) {
                $path = FileHelper::getPath($dir, $useHourDir);
                $filename = FileHelper::getSaveFileName($result, $filename, $keepFileName);
                if ($findFile = $this->exist($path, $filename)) {
                    return $dir . '/' . $findFile;
                }
                if ($result = $this->copy($path . '/' . $filename, $result)) {
                    return ['success' => $this->getUrl($result)];
                }
                return ['error' => 'Ошибка сохранения из временного фала!'];
            }
            return ['error' => 'Ошибка сохранения файла!'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
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

    /**
     * Сохранение файла через форму.
     * $files - object сохраняемые файлы
     * $useHourDir - bool нужна ли директория с днем и часом
     * $dir - string - папка внутри web/files
     * $keepfilename - bool - добавлять ли в имя файла хеш файла
     * @return array
     */
    public function upload($files, $useHourDir = true, $dir = '', $keepfilename = false) {
        try {
            $filename = FileHelper::getUploadFileName($files, $keepfilename);
            $path = FileHelper::getPath($dir, $useHourDir);
            $object = $path . '/' . $filename;
            //dd($files->tempName, $object)
            if ($file = $this->copy($object, $files->tempName)) {
                return ['success' => $this->getUrl($file)];
            }
            return ['error' => 'Не удалось сохранить файл!'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function renameFile($file, $filename) {
        $info = pathinfo($file);
        if (rename($file, $info['dirname'] . '/' . $filename)) {
            return $info['dirname'] . '/' . $filename;
        }
        return $file;
    }

}