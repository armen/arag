<?php defined('SYSPATH') or die('No direct script access.');
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
    public function __construct($config = Null)
    {
        $config = defined('MASTERAPP') ? 'default' : $config;
        $config = ($config == Null) ? Config::item('sites/'.APPNAME.'.database') : $config;

        // Load the database into the model
        if (Event::has_run('system.pre_controller')) {
            $this->db = isset(Kohana::instance()->db) ? Kohana::instance()->db : new Database($config);
        } else {
            $this->db = new Database($config);
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
        Config::set('core.include_paths', array_unique(array_merge($old_include_paths, Array(APPPATH.'modules/'.$module))));

        $model = ucfirst(strtolower($model)).'_Model';
        $model = new $model();

        return $model;
    }
    // }}}    
}

?>
