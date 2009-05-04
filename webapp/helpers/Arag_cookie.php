<?php

class cookie extends cookie_Core {

    /**
     * Sets a cookie with the given parameters.
     *
     * @param   string   cookie name or array of config options
     * @param   string   cookie value
     * @param   integer  number of seconds before the cookie expires
     * @param   string   URL path to allow
     * @param   string   URL domain to allow
     * @param   boolean  HTTPS only
     * @param   boolean  HTTP only (requires PHP 5.2 or higher)
     * @return  boolean
     */
    public static function set($name, $value = NULL, $expire = NULL, $path = NULL, $domain = NULL, $secure = NULL, $httponly = NULL)
    {
        $ob = ini_get('output_buffering');

        if (headers_sent() && (bool) $ob === false || strtolower($ob) == 'off') {
            return FALSE;
        }

        // If the name param is an array, we import it
        is_array($name) and extract($name, EXTR_OVERWRITE);

        // Fetch default options
        $config = Kohana::config('cookie');

        foreach (array('value', 'expire', 'domain', 'path', 'secure', 'httponly') as $item) {
            if ($$item === NULL AND isset($config[$item])) {
                $$item = $config[$item];
            }
        }

        if ($expire < 0) {
            return parent::set($name, $value, $expire, $path, $domain, $secure, $httponly);
        }

        header('Set-Cookie: ' . rawurlencode($name) . '=' . rawurlencode($value)
               . (empty($expire) ? '' : '; Max-Age=' . $expire)
               . (empty($path)   ? '' : '; path=' . $path)
               . (empty($domain) ? '' : '; domain=' . $domain)
               . (!$secure       ? '' : '; secure')
               . (!$httponly     ? '' : '; HttpOnly'), false);

        return true;
    }

    /**
     * Nullify and unset a cookie.
     *
     * @param   string   cookie name
     * @param   string   URL path
     * @param   string   URL domain
     * @return  boolean
     */
    public static function delete($name, $path = NULL, $domain = NULL)
    {
        if ( ! isset($_COOKIE[$name]))
            return FALSE;

        // Delete the cookie from globals
        unset($_COOKIE[$name]);

        // Sets the cookie value to an empty string, and the expiration to 24 hours ago
        return cookie::set($name, '', -864000, $path, $domain, FALSE, FALSE);
    }
}
