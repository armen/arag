<?php
 
class arr extends arr_Core 
{
    // {{{ standardize_files_array
    function standardize_files_array($array) 
    {
        if (!is_array($array)) { // No file upload
            return array();
        }
        foreach ($array as &$element) {
            if (isset($element['name'])) { //Its a file list
                if (is_array($element['name'])) {
                    $element = arr::standardize_files_array_alter($element);
                }
            } else { //Going deeper
                $element = arr::standardize_files_array($element);
            }
        }
        return $array;
    }
    // }}}
    // {{{ standardize_files_array_alter
    function standardize_files_array_alter($array) 
    {
        foreach ($array as $property_name => $property_value) {
            foreach ($property_value as $file_index => $file_value) {
                $files[$file_index][$property_name] = $array[$property_name][$file_index];
            }
        }
        return $files;
    }
    // }}}
}
