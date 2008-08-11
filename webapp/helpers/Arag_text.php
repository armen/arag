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
 * text Class
 *
 * @category    Helper
 *
 */

class text extends text_Core {

    // {{{ strrtrim
    public static function strrtrim($string, $subject, $trim_all = true)
    {
        $length = strlen($subject);
        (substr($string, -$length) === $subject) AND $string = substr($string, 0, -$length);
        ($trim_all AND substr($string, -$length) === $subject) AND $string = self::strrtrim($string, $subject);

        return $string;
    }
    // }}}
    // {{{ strltrim
    public static function strltrim($string, $subject, $trim_all = true)
    {
        $length = strlen($subject);
        (substr($string, 0, $length) === $subject) AND $string = substr($string, $length);
        ($trim_all AND substr($string, 0, $length) === $subject) AND $string = self::strltrim($string, $subject);

        return $string;
    }
    // }}}
    // {{{ strtrim
    public static function strtrim($string, $subject, $trim_all = true)
    {
        $string = self::strrtrim($string, $subject, $trim_all);
        $string = self::strltrim($string, $subject, $trim_all);

        return $string;
    }
    // }}}

} // End text
