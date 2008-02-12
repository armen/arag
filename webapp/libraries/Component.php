<?php
/**
 * Arag
 *
 * @package      Arag
 * @author       Armen Baghumian
 * @since        Version 0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Component Class
 *
 * Components base class
 *
 * @package     Arag
 * @subpackage  Libraries
 * @author      Armen Baghumian
 * @category    Component
 */
class Component_Core {
    
    // {{{ Constructor
    function __construct($namespace = Null)
    {
        $classname  = str_replace('_component', '', strtolower(get_class($this)));
        $name       = empty($namespace) ? $classname . Router::$module : $namespace;        
        $controller = Kohana::instance();        

        // Add component plugins directory to plugins_dir
        $controller->smarty->plugins_dir[] = MODPATH.$classname.'/views/plugins';
        array_unique($controller->smarty->plugins_dir);

        // Add template dir to secure_dir list
        $controller->smarty->secure_dir[] = MODPATH.$classname.'/views/';

        $controller->smarty->assign('_'.$classname, $name);        
        $controller->smarty->assign($name.'_namespace', $namespace);
        $controller->smarty->assign_by_ref($name, $this);         
    }
    // }}}
}

?>
