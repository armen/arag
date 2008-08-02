<?php

spl_autoload_extensions(EXT);
spl_autoload_register(array('Arag', 'auto_load'));

class Arag {

    // {{{ find_file
    /**
     * A wrapper around the Kohana::find_file
     */
    public static function find_file($module, $directory, $filename, $required = FALSE, $ext = FALSE)
    {
        if (!empty($module)) {
            // Change include_once to module path
            Kohana::config_set('core.modules', array_unique(array_merge(Kohana::include_paths(), Array(MODPATH.$module))));
        }

        return Kohana::find_file($directory, $filename, $required, $ext);
    }
    // }}}
    // {{{ auto_load
    /**
     * Replacement for the Kohana::auto_load
     */
    public static function auto_load($class)
    {
        static $prefix, $module;

        // Set the extension prefix
        empty($prefix) and $prefix = Kohana::config('core.extension_prefix');
        empty($module) and $module = class_exists('Router', FALSE) ? Router::$module : NULL;

        if (class_exists($class, FALSE))
            return TRUE;

        if (($type = strrpos($class, '_')) !== FALSE) {
            // Find the class suffix
            $type = substr($class, $type + 1);
        }

        switch($type) {
            case 'Component':
                $type   = 'component';
                $file   = strtolower(substr($class, 0, -10)); // Lowercase filename
                $module = $file;
                break;

            default:
                return False;
        }

        // If the file doesn't exist, just return
        if (($filepath = self::find_file($module, $type, $file)) === FALSE) {
            return FALSE;
        }

        // Load the requested file
        require_once $filepath;

        return class_exists($class, FALSE);
    }
    // }}}
}
