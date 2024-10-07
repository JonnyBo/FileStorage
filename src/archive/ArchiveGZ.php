<?php
namespace jonnybo\FileStorage\archive;

class ArchiveGZ
{

    public static function unzip($fileaddr, $path)
    {
        $buffer_size = 4096;
        $out_file_name = str_replace('.gz', '', basename($fileaddr));
        $file = gzopen($fileaddr, 'rb');
        $out_file = fopen($path . '/' . $out_file_name, 'wb');
        while (!gzeof($file)) {
            fwrite($out_file, gzread($file, $buffer_size));
        }
        fclose($out_file);
        gzclose($file);
        File::setFileRules($path);
        return ['path' => $path, 'files' => File::getFilesInPath($path)];
    }

}