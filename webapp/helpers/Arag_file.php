<?php

class file extends file_Core {

    public static function extension($filename)
    {
        // Return the extension of the filename
        return ltrim(substr($filename, strrpos($filename, '.')), '.');
    }
    public static function is_path_absolute($path)
    {
        if ($path[0] == '/' || $path[0] == '\\' ||
            (strlen($path) > 3 && ctype_alpha($path[0]) && $path[1] == ':' && ($path[2] == '\\' || $path[2] == '/'))
           )
        {
            return true;
        }

        return false;
    }

} // End file
