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
 * Router Class
 *
 * Parses URIs and determines routing
 *
 * @package       Arag
 * @subpackage    Libraries
 * @author        Armen Baghumian
 * @author        Kohana Team
 * @copyright     Copyright (c) 2007 Kohana Team 
 * @category      Router
 */
class Router extends Router_Core {
    
    // {{{ Properties
        
    public static $module          = False;
    public static $request_method  = Null;
    public static $request_methods = Array('GET' => 'read', 'POST' => 'write', 'PUT' => 'create', 'DELETE' => 'remove');

    // }}}
    // {{{ setup
    /**
     * Router setup routine
     *
     * @access public
     * @return void
     */
    public static function setup()
    {
        if ( ! empty($_SERVER['QUERY_STRING'])) {
            // Set the query string to the current query string
            self::$query_string = '?'.trim($_SERVER['QUERY_STRING'], '&');
        }
    
        // Aet the request method
        self::request_method();

        // Set all modules in core.modules so we can fetch all routes
        $old_include_paths = Config::include_paths();
        Config::set('core.modules', array_unique(array_merge($old_include_paths, glob(MODPATH.'*', GLOB_ONLYDIR))));

        if (self::$routes === NULL) {
            // Load routes
            self::$routes = Config::item('routes');
        }

        // Default route status
        $default_route = FALSE;        

        if (self::$current_uri === '') {
            // Make sure the default route is set
            if ( ! isset(self::$routes['_default']))
                throw new Kohana_Exception('core.no_default_route');

            // Use the default route when no segments exist
            self::$current_uri = self::$routes['_default'];

            // Default route is in use
            $default_route = TRUE;
        }

        // Make sure the URL is not tainted with HTML characters
        self::$current_uri = html::specialchars(self::$current_uri, FALSE);

        // At this point segments, rsegments, and current URI are all the same
        self::$segments = self::$rsegments = self::$current_uri = trim(self::$current_uri, '/');

        // Set the complete URI
        self::$complete_uri = self::$query_string.self::$current_uri;

        // Explode the segments by slashes
        self::$segments = ($default_route === TRUE OR self::$segments === '') ? array() : explode('/', self::$segments);

        if ($default_route === FALSE AND count(self::$routes) > 1) {
            // Custom routing
            self::$rsegments = self::routed_uri(self::$current_uri);
        }

        // Routed segments will never be empty
        self::$rsegments = explode('/', self::$rsegments); 
        
        // Find requested module
        $rsegments    = self::$rsegments;
        self::$module = current(array_splice($rsegments, 0, 1));        

        // Set requested module in core.module
        $include_paths = array_unique(array_merge($old_include_paths, Array(MODPATH.self::$module)));
        Config::set('core.modules', $include_paths);

        // Prepare to find the controller
        $controller_path = '';
        $method_segment  = NULL;

        foreach ($rsegments as $key => $segment) {
            // Add the segment to the search path
            $controller_path .= $segment;

            $found = FALSE;
            foreach (Config::include_paths() as $dir) {
                // Search within controllers only
                $dir .= 'controllers/';

                if (file_exists($dir.$controller_path) OR file_exists($dir.$controller_path.EXT)) {
                    // Valid path
                    $found = TRUE;

                    if (is_file($dir.$controller_path.EXT)) {
                        // Set controller name
                        self::$controller = $segment;

                        // Change controller path
                        self::$controller_path = $dir.$controller_path.EXT;

                        // Set the method segment
                        $method_segment = $key + 1;
                    }
                }
            }

            if ($found === FALSE) {
                // Maximum depth has been reached, stop searching
                break;
            }

            // Add another slash
            $controller_path .= '/';
        }

        if ($method_segment !== NULL AND isset($rsegments[$method_segment])) {
            // Set method
            self::$method = $rsegments[$method_segment];

            if (isset($rsegments[$method_segment + 1])) {
                // Set arguments
                self::$arguments = array_slice($rsegments, $method_segment + 1);
            }
        } else  {
            // Append default method
            self::$rsegments[] = self::$method;
        }
        
        // Last chance to set routing before a 404 is triggered
        Event::run('system.post_routing');

        if (self::$controller === NULL) {
            // No controller was found, so no page can be rendered
            Event::run('system.404');
        }
    }
    // }}}
    // {{{ request_method
    public static function request_method($method = Null)
    {
        if ($method != Null && in_array(strtolower($method), self::$request_methods)) {
            self::$request_method = strtolower($method);
        }

        if (self::$request_method == Null) {
        
            if (isset($_SERVER['REQUEST_METHOD'])) {
                $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

            } elseif (isset($_ENV['REQUEST_METHOD'])) {
                $REQUEST_METHOD = $_ENV['REQUEST_METHOD'];
            }

            if (isset($REQUEST_METHOD)) {

                switch($REQUEST_METHOD) {
                    case 'POST':
                        self::request_method(self::$request_methods['POST']);
                        break;

                    case 'PUT':
                        self::request_method(self::$request_methods['PUT']);
                        break;

                    case 'DELETE':
                        self::request_method(self::$request_methods['DELETE']);
                        break;

                    default:
                        self::request_method(self::$request_methods['GET']);
                }
            }
        }

        return self::$request_method;
    }
    // }}}    
}
