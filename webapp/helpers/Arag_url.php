<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Class: url
 *  URL helper class.
 *
 * Kohana Source Code:
 *  author    - Kohana Team
 *  copyright - (c) 2007 Kohana Team
 *  license   - <http://kohanaphp.com/license.html>
 */
class url extends url_Core {

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

} // End url
