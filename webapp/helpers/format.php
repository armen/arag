<?php
/**
 * Arag
 *
 * @package      Arag
 * @author       Armen Baghumian
 * @author       Roham Rafii Tehrani
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

class format_Core { 

    // {{{ date
    public static function date($timestamp)
    {
        $lang = Config::item('locale.lang');

        if ($lang === 'en') {
            $date = date('Y-m-d', $timestamp);
        } else {
            $date = date::gregorian_to_jalali((int) $timestamp);
            $date = $date['year'].'/'.$date['month'].'/'.$date['day'];
        }

        return $date;    
    }
    // }}}
}