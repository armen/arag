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
    public static function money($string, $decimals=2, $dec_point=".", $thousands_sep=",", $currency = False)
    {
        if ($currency === True) {
            $lang     = Kohana::config('locale.lang');
            $currency = Kohana::config('locale.currency.'.$lang);
        }

        if (is_numeric($string)) // check if it's a number
        {
            $string =  number_format($string, $decimals, $dec_point, $thousands_sep);
        }

        if ($currency) {
            return $string.' '._($currency);
        }

        return $string;
    }
    // }}}
    // {{{ show_date
    public function show_date($row, $field)
    {
        return format::date($row[$field], false);
    }
    // }}}
    // {{{ to_farsi
    public function to_farsi($string)
    {
        return str_replace(array(1,2,3,4,5,6,7,8,9,0), array('۱','۲','۳','۴','۵','۶','۷','۸','۹','۰'), $string);
    }
    // }}}
    // {{{ filter_amount
    public function filter_amount($field)
    {
        if (is_array($field)) {
            foreach ($field as &$value) {
                $value = str_replace(',', '', $value);
            }
        } else {
            $field = str_replace(',', '', $field);
        }

        return $field;
    }
    // }}}
}
