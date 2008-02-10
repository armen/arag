<?php defined('SYSPATH') or die('No direct script access.');

spl_autoload_unregister(array('Kohana', 'auto_load'));
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
            Config::set('core.modules', array_unique(array_merge(Config::include_paths(), Array(MODPATH.$module))));
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
        static $prefix;

        // Set the extension prefix
        empty($prefix) and $prefix = Config::item('core.extension_prefix');

        if (class_exists($class, FALSE))
            return TRUE;

        if (($type = strrpos($class, '_')) !== FALSE) {
            // Find the class suffix
            $type = substr($class, $type + 1);
        }

        $module = class_exists('Router', FALSE) ? Router::$module : NULL;        

        switch($type) {
            case 'Core':
                $type = 'libraries';
                $file = substr($class, 0, -5);
            break;
            case 'Controller':
                $type = 'controllers';
                // Lowercase filename
                $file = strtolower(substr($class, 0, -11));
            break;
            case 'Model':
                $type = 'models';
                // Lowercase filename
                $file = strtolower(substr($class, 0, -6));
            break;
            case 'Driver':
                $type = 'libraries/drivers';
                $file = str_replace('_', '/', substr($class, 0, -7));
            break;
            case 'Component':
                $type   = 'component';
                $file   = strtolower(substr($class, 0, -10)); // Lowercase filename
                $module = $file;
            break;            
            default:
                // This can mean either a library or a helper, but libraries must
                // always be capitalized, so we check if the first character is
                // lowercase. If it is, we are loading a helper, not a library.
                $type = (ord($class[0]) > 96) ? 'helpers' : 'libraries';
                $file = $class;
            break;
        }

        // If the file doesn't exist, just return
        if (($filepath = self::find_file($module, $type, $file)) === FALSE) {
            return FALSE;
        }

        // Load the requested file
        require_once $filepath;

        if ($type === 'libraries' OR $type === 'helpers') {
            if ($extension = self::find_file($module, $type, $prefix.$class)) {
                // Load the class extension
                require_once $extension;
            } elseif (substr($class, -5) !== '_Core' AND class_exists($class.'_Core', FALSE)) {
                // Transparent class extensions are handled using eval. This is
                // a disgusting hack, but it works very well.
                eval('class '.$class.' extends '.$class.'_Core { }');
            }
        }

        return class_exists($class, FALSE);    
    }
    // }}}
}
