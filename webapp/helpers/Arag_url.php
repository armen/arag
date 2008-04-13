<?php
/**
 * Arag
 *
 * @package      Arag
 * @author       Armen Baghumian
 * @since        Version 0.3
 * $Id$
 */

// ------------------------------------------------------------------------

/**
 * url Class
 *
 * @category    Helper
 *
 */

class url extends url_Core {

    // {{{ site
    /**
     * Method: site
     *  Fetches a site URL based on a URI segment.
     *
     * Parameters:
     *  uri      - site URI to convert
     *  protocol - non-default protocol
     *
     * Returns:
     *  A URL string.
     */
    public static function site($uri = '', $protocol = FALSE)
    {
        $uri = preg_match('|^[a-zA-Z]{2}/|', rtrim($uri, '/').'/') ? $uri : Config::item('locale.lang'). '/' . $uri;

        return parent::site($uri, $protocol);
    }
    // }}}
    // {{{
    /**
     * Generates routed URI from given URI.
     *
     * @param  string  URI to convert
     * @return string  Routed uri
     */
    public static function routed_uri($uri)
    {
        $routes = Config::item('routes');
        $uri    = $routed_uri = trim($uri, '/');
    
        if (isset($routes[$uri])) {
            // Literal match, no need for regex
            $routed_uri = $routes[$uri];
        
        } else {

            // Loop through the routes and see if anything matches
            foreach ($routes as $key => $val) {
                if ($key === '_default' OR $key === '_allowed') continue;

                // Trim slashes
                $key = trim($key, '/');
                $val = trim($val, '/');

                // Does this route match the current URI?
                if (preg_match('#^'.$key.'$#u', $uri)) {
                    // If the regex contains a valid callback, we'll use it
                    if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE) {
                        $routed_uri = preg_replace('#^'.$key.'$#u', $val, $uri);
                    } else {
                        $routed_uri = $val;
                    }

                    // A valid route was found, stop parsing other routes
                    break;
                }
            }
        }

        // Check router one more time to do some magic
        if (isset($routes[$routed_uri])) {
            $routed_uri = $routes[$routed_uri];
        }

        return $routed_uri;
    }
    // }}}

} // End url
