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

        return parent::site($uri, $protocol);
    }
    // }}}

} // End url
