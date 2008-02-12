<?php
/**
 * Arag
 *
 * @package      Arag
 * @author       Armen Baghumian
 * @since        Version 0.3
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Model Class
 *
 * @package     Arag
 * @subpackage  Libraries
 * @author      Armen Baghumian
 * @category    Model
 */
class Model extends Model_Core {

    // {{{ Constructor
    public function __construct($database = Null)
    {
        static $db;

        if (is_object($database) AND ($database instanceof Database)) {
            // Use the passed database instance
            $this->db = $database;
        } else {

            $config = defined('MASTERAPP') ? 'default' : Config::item('sites/'.APPNAME.'.database');
        
            // Load the default database if necessary
            ($db === NULL) and $db = new Database($config);

            // Use the static database
            $this->db = $db;
        }
    }
    // }}}
    // {{{ load
    public static function load($model = False, $module = False)
    {
        if ($module == False) {
            $module = Router::$module;
        }

        // Save old include paths
        $old_include_paths = Config::include_paths();

        // Change include_once to module path
        Config::set('core.modules', array_unique(array_merge($old_include_paths, Array(MODPATH.$module))));

        $model = ucfirst(strtolower($model)).'_Model';
        $model = new $model();

        return $model;
    }
    // }}}    
}

?>
