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
    public function __construct()
    {
        if (Event::has_run('system.pre_controller')) {
            // Load the database into the model
            $this->db = isset(Kohana::instance()->db) ? Kohana::instance()->db : new Database('default');
        } else {
            $this->db = new Database('default');
        }
    }
    // }}}
    // {{{ load
    public static function load($model = False, $module = False)
    {
        if ($module == False) {
            $module = Router::$module;
        }

        // Load the model
        Config::set('core.include_paths', Array(APPPATH.'modules/'.$module));

        $model = ucfirst(strtolower($model)).'_Model';
        $model = new $model();

        // Reset the include_paths
        Config::set('core.include_paths', Array(APPPATH.'modules/'.Router::$module));

        return $model;
    }
    // }}}    
}

?>
