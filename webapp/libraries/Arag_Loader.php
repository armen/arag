<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
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
class Arag_Loader extends CI_Loader {

    // {{{ Properties

    private $_ci_components = Array();

    // }}}
    // {{{ Constructor
    function Arag_Loader()
    {
        parent::CI_Loader();
        
        global $RTR;
        $moduleName          =  $RTR->fetch_module();
        $this->_ci_view_path =  APPPATH.'modules/'.$moduleName.'/templates/';
    }
    // }}}
    // {{{ view
    /**
     * Load View
     *
     * This function is used to load a "view" file.  It has three parameters:
     *
     * 1. The name of the "template" file.
     * 2. An associative array of data to be extracted for use in the template.
     * 3. True/False - whether to return the data or load it.  In
     *    some cases it's advantageous to be able to return data so that
     *    a developer can process it in some way.
     *
     * @access   public
     * @param    string
     * @param    array
     * @param    bool
     * @return   void
     */
    function view($template, $vars = array(), $return = False)
    {
        // Get router instance
        global $RTR;

        // Get Controller instance
        $CI =& get_instance();

        // Check if we should use smarty or not
        if ($CI->config->item('Arag_smarty_integriation') == False) {
            $output = parent::view($template, $vars, True);

        } else {

            // Okey, integriation is enabled

            if (is_array($vars)) {
                $this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $vars);
                $vars                  = $this->_ci_cached_vars;
            }

            // Assign variables to the template
            if (count($vars) > 0) {
                foreach ($vars as $key => $val) {
                    $CI->smarty->assign($key, $val);
                }
            }
        
            $template = (strpos($template, ARAG_TPL_EXT) === False)?$template.ARAG_TPL_EXT:$template;

            if (file_exists($this->_ci_view_path.$template)) {
                $template = $this->_ci_view_path.$template;
            } else {
                $template = $CI->config->item('Arag_templates_path') . $template;
            }

            // Fetch the output
            $output = $CI->smarty->fetch($template);
        }

        if ($return) {
            return $output;
        }

        // What is callig method name?
        $backtrace      = debug_backtrace();
        $calling_method = $backtrace[1]['function'];        

        // Remove the extension and replace the dot with underscore
        // $template_name = str_replace(ARAG_TPL_EXT, '', $template);
        // $template_name = str_replace('/', '_', $template_name);

        // $slot_name = $RTR->fetch_module().'_'.$RTR->fetch_class().'_'.$calling_method;
        $slot_name = $calling_method;

        // Is calling method the requested method?
        if ($RTR->fetch_method() == $calling_method || $calling_method == '_invalid_request') {
            $slot_name = 'content';
        }        
        
        $CI->output->set_output($output, $slot_name, True);
    }
    // }}}
    // {{{ model
    /**
     * Model Loader
     *
     * This function lets users load and instantiate models.
     *
     * @access    public
     * @param    string    the name of the class
     * @param    mixed    any initialization parameters
     * @return    void
     */    
    function model($model, $name = '', $db_conn = FALSE)
    {
        // Get module name
        global $RTR;
        $module = $RTR->fetch_module();

        if (is_array($model)) {
            list($model, $module) = $model;
        }

        if ($model == '') {
            return;
        }

        // Is the model in a sub-folder? If so, parse out the filename and path.
        if (strpos($model, '/') === FALSE) {
            $path = '';
        } else {
            $x = explode('/', $model);
            $model = end($x);            
            unset($x[count($x)-1]);
            $path = implode('/', $x).'/';
        }
    
        if ($name == '') {
            $name = $model;
        }
        
        if (in_array($name, $this->_ci_models, TRUE)) {
            return;
        }
        
        $CI =& get_instance();
        if (isset($CI->$name)) {
            show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
        }

        $model = strtolower($model);

        if ( ! file_exists(APPPATH.'modules/'.$module.'/models/'.$path.$model.EXT)) {

            // Check the global models
            if ( ! file_exists(file_exists(APPPATH.'models/'.$path.$model.EXT))) {
                $this->global_model($model, $name, $db_conn);
                return;

            } else {

                show_error('Unable to locate the model you have specified: '.$model);
            }
        }
                
        if ($db_conn !== FALSE AND ! class_exists('CI_DB')) {
            if ($db_conn === TRUE)
                $db_conn = '';
        
            $CI->load->database($db_conn, FALSE, TRUE);
        }
    
        if ( ! class_exists('Model')) {
            require_once(BASEPATH.'libraries/Model'.EXT);
        }

        require_once(APPPATH.'modules/'.$module.'/models/'.$path.$model.EXT);

        $model = ucfirst($model);
                
        $CI->$name = new $model();
        $CI->$name->_assign_libraries();
        
        $this->_ci_models[] = $name;    
    }
    // }}}
    // {{{ global_model
    function global_model($model, $name = '', $db_conn = FALSE)
    {
        parent::model($model, $name, $db_conn);
    }
    // }}}
    // {{{ component
    function component($component, $namespace = Null)
    {
        // Get router instance
        global $RTR;

        // Get Controller instance
        $CI =& get_instance();
    
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
        $name            = (!$namespace) ? $component . $RTR->fetch_method() : $namespace;

        if (in_array($object_name, $this->_ci_components, True)) {
            return;
        }        

        if (isset($CI->$object_name)) {
            show_error('The component name you are loading is the name of a resource that is already being used: '.$name);
        }
    
        if (!file_exists(APPPATH.'components/'.$component_lower.'/component/'.$path.$component_lower.EXT)) {
            show_error('Unable to locate the component you have specified: '.$component);
        }
                
        if (!class_exists('Component')) {
            include_once(APPPATH.'libraries/Component'.EXT);
        }

        include_once(APPPATH.'components/'.$component_lower.'/component/'.$path.$component_lower.EXT);

        $CI->$object_name = new $component($namespace);

        // Add component plugins directory to plugins_dir
        $CI->smarty->plugins_dir[] = APPPATH.'components/'.$component_lower.'/plugins';
        array_unique($CI->smarty->plugins_dir);        

        // Add template dir to secure_dir list
        $CI->smarty->secure_dir[] = APPPATH.'components/'.$component_lower.'/templates/';

        // Send component to template
        if (isset($CI->$object_name)) {
            $CI->smarty->append('_'.$component_lower, $name);        
            $CI->smarty->append($name.'_namespace', $namespace);
            $CI->smarty->append_by_ref($name, $CI->$object_name);            
        }

        $this->_ci_components[] = $object_name;        
    }
    // }}}
    // {{{ helper
    /**
     * Load Helper
     *
     * This function loads the specified helper file.
     *
     * @access   public
     * @param    mixed
     * @return   void
     */
    function helper($helpers = array())
    {
        // To load the helpers call the parent method
        parent::helper($helpers);

        $CI =& get_instance();

        if ($CI->config->item('Arag_smarty_integriation') == True) {

            // Okey, integriation is enabled

            if ( !is_array($helpers)) {
                $helpers = Array($helpers);
            }
    
            foreach ($helpers as $helper) {

                if (is_dir(APPPATH.'helpers/'.$helper)) { 

                    // Load the helper wrappers for the helper              
                    $CI->smarty->plugins_dir[] = APPPATH.'helpers/'.$helper;
                    array_unique($CI->smarty->plugins_dir);

                } else {
                    show_error('Unable to load the requested helpers in directory: '.APPPATH.'helpers/'.$helper);
                }
            }
        }        
    }
    // }}}    
    // {{{ decorator
    function decorator($decorator)
    {
        $CI =& get_instance();
        $CI->output->set_decorator($decorator);
    }
    // }}}
    // {{{ get_view_path
    function get_view_path()
    {
        return $this->_ci_view_path;
    }
    // }}}
}

?>
