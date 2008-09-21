<?php

class file extends file_Core {

    public static function extension($filename)
    {
        // Return the extension of the filename
        return ltrim(substr($filename, strrpos($filename, '.')), '.');
    }

} // End file
