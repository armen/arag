<?php

class cookie {

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

        header('Set-Cookie: ' . rawurlencode($name) . '=' . rawurlencode($value)
               . (empty($expire) ? '' : '; Max-Age=' . $expire)
               . (empty($path)   ? '' : '; path=' . $path)
               . (empty($domain) ? '' : '; domain=' . $domain)
               . (!$secure       ? '' : '; secure')
               . (!$httponly     ? '' : '; HttpOnly'), false);

        return true;
    }
}
