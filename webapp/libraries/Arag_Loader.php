<?php defined('SYSPATH') or die('No direct script access.');
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
 * Loader Class
 *
 * Loads views and files
 *
 * @package     Arag
 * @subpackage  Libraries
 * @author      Armen Baghumian
 * @category    Loader
 */
class Loader extends Loader_Core {

    // {{{ Properties

    private $components = Array();

    // }}}
    // {{{ decorator
    public function decorator($decorator)
    {
        return Kohana::instance()->decorator = parent::view($decorator);
    }
    // }}}
    // {{{ view
    public function view($name, $data = Array())
    {
        $controller       = Kohana::instance();
        $controller->view = parent::view($name, $data);

        if ($controller->decorator instanceof View) {        
            // What is callig method name?
            // Remove appended requested method from function name
            $backtrace      = debug_backtrace();
            $calling_method = substr($backtrace[1]['function'], 0, 1) != '_' ? $backtrace[1]['function'] : $backtrace[2]['function'];            
            $calling_method = preg_replace('/_('.implode('|', Router::$request_methods).')(_error)?/', '', $calling_method);
            $slot           = $calling_method;        

            // Is calling method the requested method?
            if (Router::$method == $calling_method || $calling_method == '_invalid_request') {
                $slot = 'content';
            }

            $controller->slots[$slot] = $controller->view;
        }

        return $controller->view;
    }
    // }}}
    // {{{ vars
    public function vars($vars)
    {
        $controller = Kohana::instance();

        if ($controller->decorator instanceof View) {

            // What is callig method name?
            // Remove appended requested method from function name
            $backtrace      = debug_backtrace();
            $calling_method = substr($backtrace[1]['function'], 0, 1) != '_' ? $backtrace[1]['function'] : $backtrace[2]['function'];           
            $calling_method = preg_replace('/_('.implode('|', Router::$request_methods).')(_error)?/', '', $calling_method);
            $slot           = $calling_method;        

            // Is calling method the requested method?
            if (Router::$method == $calling_method || $calling_method == '_invalid_request') {
                $slot = 'content';
            }

            $controller->vars[$slot] = (isset($controller->vars[$slot]) && is_array($controller->vars[$slot])) ? 
                                       array_merge($controller->vars[$slot], (array)$vars) :
                                       (array)$vars;
        }
    }
    // }}}
    // {{{ component
    public function component($component, $namespace = Null)
    {
        $controller = Kohana::instance();
    
        if ($component == '') {
            return;
        }

        // Is the component in a sub-folder? If so, parse out the filename and path.
        if (strpos($component, '/') === False) {
            $path = '';
        } else {
            $x = explode('/', $component);
            $component = end($x);            
            unset($x[count($x)-1]);
            $path = implode('/', $x).'/';
        }

        $component_lower = strtolower($component);
        $object_name     = (!$namespace) ? $component : $namespace;
        $name            = (!$namespace) ? $component . Router::$module : $namespace;

        if (in_array($object_name, $this->components, True)) {
            return;
        }        

        if (isset($controller->$object_name)) {
            Kohana::show_error('Resource already exists', 
                               'The component name you are loading is the name of a resource that is already being used: '.$name);
        }
    
        if (!file_exists(APPPATH.'components/'.$component_lower.'/component/'.$path.$component_lower.EXT)) {
            Kohana::show_error('Component not found', 'Unable to locate the component you have specified: '.$component);
        }
                
        if (!class_exists('Component')) {
            include_once(APPPATH.'libraries/Component'.EXT);
        }

        include_once(APPPATH.'components/'.$component_lower.'/component/'.$path.$component_lower.EXT);

        $controller->$object_name = new $component($namespace);

        // Add component plugins directory to plugins_dir
        $controller->Arag_Smarty->plugins_dir[] = APPPATH.'components/'.$component_lower.'/plugins';
        array_unique($controller->Arag_Smarty->plugins_dir);

        // Add template dir to secure_dir list
        $controller->Arag_Smarty->secure_dir[] = APPPATH.'components/'.$component_lower.'/templates/';

        // Send component to template
        if (isset($controller->$object_name)) {
            $controller->Arag_Smarty->assign('_'.$component_lower, $name);        
            $controller->Arag_Smarty->assign($name.'_namespace', $namespace);
            $controller->Arag_Smarty->assign_by_ref($name, $controller->$object_name);
        }

        $this->components[] = $object_name;        
    }
    // }}}
    // {{{ model
    public function model($name, $alias = False, $module = False)
    {
        // The alias is used for Controller->alias
        $alias = ($alias == FALSE) ? $name : $alias;
        $class = ucfirst($name).'_Model';

        if (isset(Kohana::instance()->$alias)) {
            return FALSE;
        }

        if ($module == False) {
            $module = Router::$module;
        }

        // Change include_once to module path
        Config::set('core.include_paths', Array(APPPATH.'modules/'.$module));

        if (strpos($name, '/') !== FALSE) {
            // Handle models in subdirectories
            require_once Kohana::find_file('models', $name);

            // Reset the class name
            $class = end(explode('/', $class));
        }

        // Load the model
        $model = new $class();

        // Reset the include_paths
        Config::set('core.include_paths', Array(APPPATH.'modules/'.Router::$module));        

        if ($alias === TRUE)
            return $model;

        Kohana::instance()->$alias = $model;
    }
    // }}}
}

?>
