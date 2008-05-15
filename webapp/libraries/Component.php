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
    public function __construct($namespace = Null, $component = Null)
    {
        $classname  = str_replace('_component', '', strtolower(get_class($this)));   // component class name
        $component  = empty($component) ? $classname : $component;                   // component is module name
        $name       = empty($namespace) ? $component . Router::$module : $namespace;        
        $controller = Kohana::instance();        

        // Add component plugins directory to plugins_dir
        $controller->smarty->plugins_dir[] = MODPATH.$component.'/views/plugins';
        array_unique($controller->smarty->plugins_dir);

        // Add template dir to secure_dir list
        $controller->smarty->secure_dir[] = MODPATH.$component.'/views/';

        $controller->smarty->assign('_'.$classname, $name);        
        $controller->smarty->assign($name.'_namespace', $namespace);
        $controller->smarty->assign_by_ref($name, $this);         
    }
    // }}}
}

?>
