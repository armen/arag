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
 * Router Class
 *
 * Parses URIs and determines routing
 *
 * @package       Arag
 * @subpackage    Libraries
 * @author        Armen Baghumian
 * @category      Router
 */
class Arag_Router extends CI_Router {

    // {{{ Properties

    var $module;
    var $default_module;
    var $request_method = Null;
    var $methods        = Array('GET' => 'read', 'POST' => 'write', 'PUT' => 'create', 'DELETE' => 'remove');

    // }}}
    // {{{ Constructor
    function Arag_Router()
    {
        parent::CI_Router();
    }
    // }}}
    // {{{ _set_route_mapping
    /**
     * Set the route mapping
     *
     * This function determines what should be served based on the URI request,
     * as well as any "routes" that have been set in the routing config file.
     *
     * @access    private
     * @return    void
     */
    function _set_route_mapping()
    {
        // Are query strings enabled in the config file?
        // If so, we're done since segment based URIs are not used with query strings.
        if ($this->config->item('enable_query_strings') === TRUE && 
            isset($_GET[$this->config->item('controller_trigger')]) &&
            isset($_GET[$this->config->item('module_trigger')])) {

            $this->set_module(trim($this->_filter_uri($_GET[$this->config->item('module_trigger')])));
            $this->set_class(trim($this->_filter_uri($_GET[$this->config->item('controller_trigger')])));

            if (isset($_GET[$this->config->item('function_trigger')])) {
                $this->set_method(trim($this->_filter_uri($_GET[$this->config->item('function_trigger')])));
            }
            
            return;
        }
        
        // Load the routes.php file.
        @include(APPPATH.'config/routes'.EXT);
        $this->routes = ( ! isset($route) OR ! is_array($route)) ? array() : $route;
        unset($route);

        // Set the default controller so we can display it in the event
        // the URI doesn't correlated to a valid controller.
        $this->default_controller = ( ! isset($this->routes['default_controller']) || $this->routes['default_controller'] == '') ? 
                                    FALSE : strtolower($this->routes['default_controller']);

        $this->default_module = ( ! isset($this->routes['default_module']) || $this->routes['default_module'] == '') ? 
                                False : strtolower($this->routes['default_module']);
        
        // Fetch the complete URI string
        $this->uri_string = $this->_get_uri_string();

        // If the URI contains only a slash we'll kill it
        if ($this->uri_string == '/') {
            $this->uri_string = '';
        }

        // Is there a URI string? If not, the default module and controller specified in the "routes" file will be shown.
        if ($this->uri_string == '') {

            if ($this->default_module === False || $this->default_controller === False) {
                show_error("Unable to determine what should be displayed. A default route has not been specified in the routing file.");
            }

            $this->set_module($this->default_module);
            $this->set_class($this->default_controller);
            $this->set_method('index');        

            log_message('debug', "No URI present. Default module and controller set.");
            return;
        }
        unset($this->routes['default_module']);
        unset($this->routes['default_controller']);

        // Do we need to remove the suffix specified in the config file?
        if ($this->config->item('url_suffix') != "") {
            $this->uri_string = preg_replace("|".preg_quote($this->config->item('url_suffix'))."$|", "", $this->uri_string);
        }
        
        // Explode the URI Segments. The individual segments will
        // be stored in the $this->segments array.    
        foreach (explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->uri_string)) as $val) {
            // Filter segments for security
            $val = trim($this->_filter_uri($val));
            
            if ($val != '') {
                $this->segments[] = $val;
            }
        }

        // Set the specified module
        $this->set_module($this->segments[0]);

        // Load the routes.php file of requested module
        @include(APPPATH.'modules/'.$this->fetch_module().'/config/routes'.EXT);
        $this->routes = ( ! isset($route) OR ! is_array($route)) ? $this->routes : array_merge($this->routes, $route);
        unset($route);

        // Parse any custom routing that may exist
        $this->_parse_routes();        
        
        // Re-index the segment array so that it starts with 1 rather than 0
        $this->_reindex_segments();
    }
    // }}}
    // {{{ _validate_segments
    /**
     * Validates the supplied segments.  Attempts to determine the path to
     * the controller.
     *
     * @access    private
     * @param     array
     * @return    array
     */    
    function _validate_segments($segments)
    {
        if (count($segments) <= 2) {
            // XXX: URI could be /<module_name> or /<module_name>/<class_name>

            $this->set_module($segments[0]);
            // If segment was /<module_name> make class name same as module name otherwise we have it
            $this->set_class((count($segments) == 1) ? $segments[0] : $segments[1]); 
            $this->set_method('index');
            
            if ( ! file_exists(APPPATH.'modules/'.$segments[0].'/controllers/'.$this->fetch_class().EXT)) {
                
                $this->set_class($this->default_controller);

                // Does the default controller exist in the sub-folder?
                if ( ! file_exists(APPPATH.'modules/'.$segments[0].'/controllers/'.$this->default_controller.EXT)) {
                    $this->directory = '';
                    return array();
                }
            }

            return $segments;
        }

        // Does the requested controller exist in the root folder?
        if (file_exists(APPPATH.'modules/'.$segments[0].'/controllers/'.$segments[1].EXT) && 
            !is_dir(APPPATH.'modules/'.$segments[0].'/controllers/'.$segments[1])) {
            return $segments;
        }

        // Is the controller in a sub-folder?
        if (is_dir(APPPATH.'modules/'.$segments[0].'/controllers/'.$segments[1])) {

            if (file_exists(APPPATH.'modules/'.$segments[0].'/controllers/'.$segments[1].'/'.$segments[2].EXT)) {
                // There is a directory and a controller in it so
                // set the directory and remove it from the segment array
                $this->set_directory($segments[1]);
                array_splice($segments, 1, 1, Null);
            }
            
            if (count($segments) >= 2) {
                // Does the requested controller exist in the sub-folder?
                if ( ! file_exists(APPPATH.'modules/'.$segments[0].'/controllers/'.$this->fetch_directory().$segments[1].EXT)) {
                    show_404();    
                }

            } else {

                $this->set_class($this->default_controller);
                $this->set_method('index');
            
                // Does the default controller exist in the sub-folder?
                if ( ! file_exists(APPPATH.'modules/'.$segments[0].'/controllers/'.$this->fetch_directory().$this->default_controller.EXT)) {
                    $this->directory = '';
                    return array();
                }
            }
                
            return $segments;
        }
    
        // Can't find the requested controller...
        show_404();    
    }
    // }}}
    // {{{ _compile_segments
    /**
     * Compile Segments
     *
     * This function takes an array of URI segments as
     * input, and puts it into the $this->segments array.
     * It also sets the current class/method
     *
     * @access    private
     * @param    array
     * @param    bool
     * @return    void
     */
    function _compile_segments($segments = array())
    {    
        $segments = $this->_validate_segments($segments);
        
        if (count($segments) <= 1) {
            // XXX: URI could be / or /<module_name>
            return;
        }
                        
        $this->set_module($segments[0]);
        $this->set_class($segments[1]);
        
        if (isset($segments[2])) {
            // A scaffolding request. No funny business with the URL
            if ($this->routes['scaffolding_trigger'] == $segments[1] AND $segments[1] != '_ci_scaffolding') {
                $this->scaffolding_request = TRUE;
                unset($this->routes['scaffolding_trigger']);

            } else {
                // A standard method request
                $this->set_method($segments[2]);
            }
        }
        
        // Update our "routed" segment array to contain the segments.
        // Note: If there is no custom routing, this array will be
        // identical to $this->segments
        $this->rsegments = $segments;
    }
    // }}}
    // {{{ set_module
    /**
     * Set the module name
     *
     * @access    public
     * @param     string
     * @return    void
     */    
    function set_module($module)
    {
        $this->module = $module;
    }
    // }}}
    // {{{ fetch_module
    /**
     * Fetch the current module
     *
     * @access    public
     * @return    string
     */    
    function fetch_module()
    {
        return $this->module;
    }
    // }}}
    // {{{ fetch_request_method
    function fetch_request_method()
    {
        if ($this->request_method == Null) {
        
            if (isset($_SERVER['REQUEST_METHOD'])) {
                $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

            } elseif (isset($_ENV['REQUEST_METHOD'])) {
                $REQUEST_METHOD = $_ENV['REQUEST_METHOD'];
            }

            switch($REQUEST_METHOD) {
                case 'POST':
                    $this->set_request_method($this->methods['POST']);
                    break;

                case 'PUT':
                    $this->set_request_method($this->methods['PUT']);
                    break;

                case 'DELETE':
                    $this->set_request_method($this->methods['DELETE']);
                    break;

                default:
                    $this->set_request_method($this->methods['GET']);
            }
        }

        return $this->request_method;
    }
    // }}}
    // {{{ set_request_method
    function set_request_method($method)
    {
        $this->request_method = strtolower($method);
    }
    // }}}
    // {{{ fetch_request_methods
    function fetch_request_methods()
    {
        return $this->methods;
    }
    // }}}
}

?>
