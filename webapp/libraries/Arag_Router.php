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
        // Aet the request method
        self::request_method();

        // Set all modules in core.modules so we can fetch all routes
        $old_include_paths = Config::include_paths();
        Config::set('core.modules', array_unique(array_merge($old_include_paths, glob(MODPATH.'*', GLOB_ONLYDIR))));

        // Fetch all routers and save it
        self::$routes = Config::item('routes');

        // Generate segments
        self::generate_segments();
        
        // Find requested module
        $rsegments    = self::$rsegments;
        self::$module = current(array_splice($rsegments, 0, 1));        

        // Set requested module in core.module
        $include_paths = array_unique(array_merge($old_include_paths, Array(MODPATH.self::$module)));
        Config::set('core.modules', $include_paths);

        // Prepare for Controller search
        self::$directory  = '';
        self::$controller = '';

        // Path to be added to as we search deeper
        $search = '/controllers';

        // Use the rsegments to find the controller
        foreach($rsegments as $key => $segment) {
            foreach($include_paths as $path) {
                // The controller has been found, all arguments can be set
                if (is_file($path.$search.'/'.$segment.EXT)) {
                    self::$directory  = $path.$search.'/';
                    self::$controller = $segment;
                    self::$method     = isset($rsegments[$key + 1]) ? $rsegments[$key + 1] : 'index';
                    self::$arguments  = isset($rsegments[$key + 2]) ? array_slice($rsegments, $key + 2) : array();

                    // Stop searching, two levels for foreach
                    break 2;
                }
            }

            // Add the segment to the search
            $search .= '/'.$segment;
        }

        // If the controller is empty, run the system.404 event
        empty(self::$controller) and Event::run('system.404');
    }
    // }}}
    // {{{ generate_segments
    public static function generate_segments()
    {
        // Make sure the default route is set
        if ( ! isset(self::$routes['_default'])) {
            throw new Kohana_Exception('core.no_default_route');
        }

        // Use the default route when no segments exist
        if (self::$current_uri == '' OR self::$current_uri == '/') {
            self::$current_uri = self::$routes['_default'];
            $default_route = TRUE;
        } else {
            $default_route = FALSE;
        }

        if ( ! empty($_SERVER['QUERY_STRING'])) {
            // Set the query string to the current query string
            self::$query_string = '?'.trim($_SERVER['QUERY_STRING'], '&');
        }

        // At this point, set the segments, rsegments, and current URI
        // In many cases, all of these variables will match
        self::$segments = self::$rsegments = self::$current_uri = trim(self::$current_uri, '/');

        // Custom routing
        if ($default_route == FALSE AND count(self::$routes) > 1) {
            if (isset(self::$routes[self::$current_uri])) {
                // Literal match, no need for regex
                self::$rsegments = self::$routes[self::$current_uri];
            } else {
                // Loop through the routes and see if anything matches
                foreach(self::$routes as $key => $val) {
                    if ($key == '_default') continue;

                    // Does this route match the current URI?
                    if (preg_match('#^'.$key.'$#u', self::$segments)) {
                        // If the regex contains a valid callback, we'll use it
                        if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE) {
                            self::$rsegments = preg_replace('#^'.$key.'$#u', $val, self::$segments);
                        } else {
                            self::$rsegments = $val;
                        }

                        // A valid route was found, stop parsing other routes
                        break;
                    }
                }
            }

            // Check router one more time to do some magic
            self::$rsegments = isset(self::$routes[self::$rsegments]) ? self::$routes[self::$rsegments] : self::$rsegments;
        }

        // Explode the segments by slashes
        if ($default_route == TRUE OR self::$segments == '') {
            self::$segments = array();
        } else {
            self::$segments = explode('/', self::$segments);
        }
        // Routed segments will never be blank
        self::$rsegments = explode('/', self::$rsegments);

        // Validate segments to prevent malicious characters
        foreach(self::$segments as $key => $segment) {
            self::$segments[$key] = self::filter_uri($segment);
        }

        // Yah, routed segments too, even though it should never happen
        foreach(self::$rsegments as $key => $segment) {
            self::$rsegments[$key] = self::filter_uri($segment);
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

        return self::$request_method;
    }
    // }}}    
}

?>

