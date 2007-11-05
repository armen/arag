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

        // Set all modules in include_paths so we can fetch all routes
        Config::set('core.include_paths', glob(APPPATH . 'modules/*', GLOB_ONLYDIR));

        self::$routes = Config::item('routes');

        // Make sure the default route is set
        if ( ! isset(self::$routes['_default'])) {
            throw new Kohana_Exception('core.no_default_route');
        }

        // The follow block of if/else attempts to retrieve the URI segments automagically
        // Supported request_methods: CLI, GET, PATH_INFO, ORIG_PATH_INFO, PHP_SELF
        if (PHP_SAPI === 'cli') {

            // Command line requires a bit of hacking
            if (isset($_SERVER['argv'][1])) {
                self::$segments = $_SERVER['argv'][1];

                // Remove GET string from segments
                if (($query = strrpos(self::$segments, '?')) !== FALSE) {

                    list (self::$segments, $query) = explode('?', self::$segments);

                    // Insert query into GET array
                    foreach(explode('&', $query) as $pair) {
                        list ($key, $val) = array_pad(explode('=', $pair), 1, '');

                        $_GET[utf8::clean($key)] = utf8::clean($val);
                    }
                }
            }
        
        } elseif (count($_GET) === 1 AND current($_GET) == '') {
            self::$segments = current(array_keys($_GET));

            // Fixes really stange handling of a suffix in a GET string
            if ($suffix = Config::item('core.url_suffix') AND substr(self::$segments, -(strlen($suffix))) === '_'.substr($suffix, 1)) {
                self::$segments = substr(self::$segments, 0, -(strlen($suffix)));
            }

            // Destroy GET
            $_GET = array();

        } elseif (isset($_SERVER['PATH_INFO']) AND $_SERVER['PATH_INFO']) {
            self::$segments = $_SERVER['PATH_INFO'];

        } elseif (isset($_SERVER['ORIG_PATH_INFO']) AND $_SERVER['ORIG_PATH_INFO']) {
            self::$segments = $_SERVER['ORIG_PATH_INFO'];

        } elseif (isset($_SERVER['PHP_SELF']) AND $_SERVER['PHP_SELF']) {
            self::$segments = $_SERVER['PHP_SELF'];
        }

        // Find the URI string based on the location of the front controller
        if (($offset = strpos(self::$segments, KOHANA)) !== FALSE) {

            // Add the length of the index file to the offset
            $offset += strlen(KOHANA);

            // Get the segment part of the URL
            self::$segments = substr(self::$segments, $offset);
            self::$segments = trim(self::$segments, '/');
        }

        // Use the default route when no segments exist 
        if (self::$segments == '' OR self::$segments == '/') {
            self::$segments = self::$routes['_default'];
            $default_route = TRUE;
        } else {
            $default_route = FALSE;
        }

        // Remove the URL suffix
        if ($suffix = Config::item('core.url_suffix')) {
            self::$segments = preg_replace('!'.preg_quote($suffix).'$!u', '', self::$segments);
        }

        // Remove extra slashes from the segments that could cause fucked up routing
        self::$segments = preg_replace('!//+!', '/', self::$segments);

        // At this point, set the segments, rsegments, and current URI
        // In many cases, all of these variables will match
        self::$segments = self::$rsegments = self::$current_uri = trim(self::$segments, '/');

        // Custom routing
        if ($default_route == FALSE AND count(self::$routes) > 1) {

            if (isset(self::$routes[self::$current_uri])) {
                // Literal match, no need for regex
                self::$rsegments = self::$routes[self::$current_uri];
            
            } else {

                // Loop through the routes and see if anything matches
                foreach(self::$routes as $key => $val) {

                    if ($key == '_default') {
                        continue;
                    }

                    // Replace helper strings
                    $key = str_replace
                    (
                        array(':any', ':num'),
                        array('.+',   '[0-9]+'),
                        $key
                    );

                    // Does this route match the current URI?
                    if (preg_match('!^'.$key.'$!u', self::$segments)) {

                        // If the regex contains a valid callback, we'll use it
                        if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE) {
                            self::$rsegments = preg_replace('!^'.$key.'$!u', $val, self::$segments);

                        } else {
                            self::$rsegments = $val;
                        }

                        // A valid route was found, stop parsing other routes
                        break;
                    }
                }
            }
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
        if ( ! empty(self::$segments)) {
            foreach(self::$segments as $key => $segment) {
                self::$segments[$key] = self::filter_uri($segment);
            }
        }

        // Yah, routed segments too, even though it should never happen
        if ( ! empty(self::$rsegments)) {
            foreach(self::$rsegments as $key => $segment) {
                self::$rsegments[$key] = self::filter_uri($segment);
            }
        }

        // Prepare for Controller search
        self::$directory  = '';
        self::$controller = '';

        // First segmen is module name so ignore it
        $rsegments          = self::$rsegments;
        list(self::$module) = array_splice($rsegments, 0, 1);

        Config::set('core.include_paths', Array(APPPATH.'modules/'.self::$module));

        // Fetch the include paths
        $include_paths = Config::include_paths();

        // Path to be added to as we search deeper
        $search = 'controllers';

        // Use the rsegments to find the controller
        foreach($rsegments as $key => $segment) {
            
            foreach($include_paths as $path) {

                // The controller has been found, all arguments can be set
                if (strpos($path, 'modules/'.self::$module) !== False && is_file($path.$search.'/'.$segment.EXT)) {

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

        if (empty(self::$controller)) {
            Kohana::show_404();
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
