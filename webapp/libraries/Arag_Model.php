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
    public function __construct()
    {
        $config = defined('MASTERAPP') ? 'default' : Kohana::config('sites/'.APPNAME.'.database');

        // Load the default database if necessary
        $this->db = new Database($config);
    }
    // }}}
    // {{{ load
    public static function load($model = False, $module = False)
    {
        if ($module == False) {
            $module = Router::$module;
        }

        // Save old include paths
        $old_include_paths = Kohana::include_paths();

        // Change include_once to module path
        Kohana::config_set('core.modules', array_unique(array_merge($old_include_paths, Array(MODPATH.$module))));

        $model = ucfirst(strtolower($model)).'_Model';
        $model = new $model();

        return $model;
    }
    // }}}
}
