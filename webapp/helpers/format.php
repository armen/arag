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
    public static function date($timestamp, $showHours = True, $dateFormat = 'Y-m-d', $timeFormat = ' H:i:s')
    {
        // TODO: Jalali date should be able to accept date formats
        $lang = Kohana::config('locale.lang');

        if (!$timestamp) {
            return "-";
        }

        if ($lang === 'en') {
            $date = date($dateFormat, $timestamp);
            if ($showHours) {
                $date = $date . date($timeFormat, $timestamp);
            }
        } else {
            $date = date::gregorian_to_jalali((int) $timestamp);
            $date = $date['year'].'-'.$date['month'].'-'.$date['day'];
            if ($showHours) {
                $date = $date . date($timeFormat, $timestamp);
            }
        }

        return $date;
    }
    // }}}
    // {{{ money
    public static function money($string, $decimals=2, $dec_point=".", $thousands_sep=",")
    {
        if (is_numeric($string)) // check if it's a number
        {
            return number_format($string, $decimals, $dec_point, $thousands_sep);
        }
        else
        {
            return $string;
        }
    }
    // }}}
    // {{{ show_date
    public function show_date($row, $field)
    {
        return format::date($row[$field], false);
    }
    // }}}
}
