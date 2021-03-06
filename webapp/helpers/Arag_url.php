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
        if (Kohana::config('locale.multi_lingual')) {
            $uri = preg_match('|^[a-zA-Z]{2}/|', rtrim($uri, '/').'/') ? $uri : Kohana::config('locale.lang'). '/' . $uri;
        }

        static $protocols;
        if (!isset($protocols[$protocol])) {
            $protocols[$protocol] = parent::site(null, $protocol);
        }
        return $protocols[$protocol].$uri;
    }
    // }}}
    // {{{ redirect
    public static function redirect($uri = '', $method = '302')
    {
        if (!isset($GLOBALS['controller_execute'])) {
            parent::redirect($uri, $method);
        } else {
            $content = Controller::execute($uri);

            if (isset($GLOBALS['controller_execute_display'])) {
                echo $content;
            }
        }
    }
    // }}}
    // {{{ current
	public static function current($qs = FALSE, $strip_args = FALSE)
	{
        if ($qs === TRUE) {
            // Ignore strip_args when $qs is true
            return parent::current(TRUE);
        }

        return ($strip_args == TRUE) ? implode('/', array_diff(Router::$segments, Router::$arguments)) : Router::$current_uri;
	}
    // }}}

} // End url
