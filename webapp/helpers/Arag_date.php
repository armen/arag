<?php
/**
 * Arag
 *
 * @package      Arag
 * @author       Ashkan Ghasemi
 * @author       Armen Baghumian
 * @since        Version 0.3
 * @based_on     http://savannah.nongnu.org/projects/jcal
 * $Id$
 */

// ------------------------------------------------------------------------

/**
 * url Class
 *
 * @category    Helper
 *
 */

class date extends date_Core {

    // {{{ properties

    private static $jcal_jalali_month_len = array (31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
    private static $jcal_jleap_tracker = array (1, 5, 9, 13, 17, 22, 26, 30);

    private static $jcal_jalali_months = array ("Farvardin", "Ordibehesht", "Khordaad", "Tir", "Mordaad", "Shahrivar",
                                                "Mehr", "Aabaan", "Aazar", "Dey", "Bahman", "Esfand");

    private static $jalali_days = array ("Shanbeh", "Yek-Shanbeh", "Do-Shanbeh", "Seh-Shanbeh", "Chahaar-Shanbeh",
                                         "Panj-Shanbeh", "Aadineh");

    const JCAL_GLEAP = 1968;
    const JCAL_JLEAP = 1346;

    const JCAL_BASE_CAL_DAY   = 11;
    const JCAL_BASE_CAL_MONTH = 10;
    const JCAL_BASE_CAL_YEAR  = 1348;

    const JCAL_NYEAR_LEN  = 31536000; /* normal year */
    const JCAL_LYEAR_LEN  = 31622400; /* leap year */
    const JCAL_YEAR_LEN   = 31557600; /* exact */
    const JCAL_BYEAR_DIFF = 21600;
    const JCAL_DAY_LEN    = 86400;

    const JCAL_CAL_TRIGGER   = 1;
    const JCAL_CAL_DTRIGGER  = 2;
    const JCAL_UTC_D_YEAR    = 1970;
    const JCAL_GRE_JAL_DCONV = 79;
    const JCAL_JAL_GRE_DCONV = 286;

    const JCAL_SEGMENT_REDIRECTION = 0.67;
    const JCAL_YEAR_LEND           = 365.24220;
    const JCAL_PAHLAVI_DIFF        = 1180;

    const JCAL_JULIAN_FLAG    = 'j';
    const JCAL_WHOLEYEAR_FLAG = 'y';
    const JCAL_CURRENT_MONTH  = '1';
    const JCAL_THREE_MONTHS   = '3';
    const JCAL_PAHLAVI_FLAG   = 'p';
    const JCAL_SHOW_VERSION   = 'V';

    const JCAL_CAL_VERSION = '0.1.2';

    // }}}
    // {{{ is_jleap
    public static function is_jleap($year)
    {
        $pr  = $year;
        $pr -= 475;

        if ($pr < 0) {
            $pr--;
        }

        $pr %= 2820;
        if ($pr >= 2783) {
            $pr -= 2783;

            if ($pr == 0) {
                return FALSE;
            } else if ($pr % 4 == 0) {
                return TRUE;
            } else {
                return FALSE;
            }

        } else {

            $pr %= 128;

            if ($pr < 29) {

                if ($pr == 0) {
                    return FALSE;
                } else if ($pr % 4 == 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }

            } else if ($pr < 62) {

                $pr -= 29;

                if ($pr == 0) {
                    return FALSE;
                } else if ($pr % 4 == 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }

            } else if ($pr < 95) {

                $pr -= 62;

                if ($pr == 0) {
                    return FALSE;
                } else if ($pr % 4 == 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }

            } else {

                $pr -= 95;

                if ($pr == 0) {
                    return FALSE;
                } else if (($pr % 4) == 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        }

        return FALSE;
    }
    // }}}
    // {{{ is_gleap
    public static function is_gleap($year)
    {
        if ((($year-self::JCAL_GLEAP) % 4) == 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    // }}}
    // {{{ get_last_jleap
    public static function get_last_jleap($year)
    {
        for ($i=$year; $i >= $year-5; $i--) {
            if (self::is_jleap($i)) {
                return $i;
            }
        }

        return FALSE;
    }
    // }}}
    // {{{ get_last_gleap
    public static function get_last_gleap($year)
    {
        for ($i=year; $i >= $year-5; $i--) {
            if (self::is_gleap($i)) {
                return $i;
            }
        }

        return FALSE;
    }
    // }}}
    // {{{ convert_to_jalali
    /**
     * Method of conversion, used to convert days to jalali date.
     * all days are calculated from UTC Epoch.
     */
    public static function convert_to_jalali($fu_days)
    {
        $r_days  = 0;
        $n_days  = 0;
        $n_years = 0;
        $fn_days;
        $redirect;

        /* determining day of the week */
        $n_days  = $fu_days;
        $n_days %= 7;
        $n_days += 5;

        if ($n_days == -1) {
            $n_days = 6;
        }

        $wday = abs($n_days % 7);

        $fn_days = $fu_days + 287;

        /* determining current year */
        $n_years = (int) ($fu_days / self::JCAL_YEAR_LEND);
        $year = $n_years + self::JCAL_BASE_CAL_YEAR;

        $redirect = (($n_years * self::JCAL_YEAR_LEND) - floor($n_years * self::JCAL_YEAR_LEND));

        /*
         * Accurate constant is not known, probably does't work for some
         * blah-blah dates, but I'll improve it later.
         */

        if ($redirect <= 0.52) {
            $today = ceil($fn_days - ($n_years * self::JCAL_YEAR_LEND));

        } else if ($redirect <= self::JCAL_SEGMENT_REDIRECTION) {
            $today = ($fn_days - ($n_years * self::JCAL_YEAR_LEND));

        } else if (($redirect> self::JCAL_SEGMENT_REDIRECTION) && ($redirect < 0.88)) {

            $today = ($fn_days - ($n_years * self::JCAL_YEAR_LEND));
        } else {

            $today = floor($fn_days - ($n_years * self::JCAL_YEAR_LEND));
        }

        if ($fu_days < 0 && ($today) <=0 ) {
            if ($today == 0) {
                $today  = (!self::is_jleap(($year)-1)) ? 365 : 366;
            } else {
                $today += (!self::is_jleap(($year)-1)) ? 365 : 366;
            }
            $year--;
        }

        if ($today == 366) {
            $today-= (self::is_jleap($year)) ? 0 : 365;

            if (!self::is_jleap($year)) {
                $year++;
            }

        } else if ($today > 366) {
            $today -= (self::is_jleap($year)) ? 366 : 365;
            $year++;
        }

        $r_days = $today;

        for ($i=1; $i <= 11; $i++) {
            if ($r_days < self::$jcal_jalali_month_len[$i-1]+1) {
                $month = $i;
                $day   = $r_days;

                return array('year'  => (int) $year,
                             'month' => (int) $month,
                             'day'   => (int) $day,
                             'wday'  => (int) $wday,
                             'today' => (int) $today);
            }

            $r_days -= self::$jcal_jalali_month_len[$i-1];
        }

        return array('year'  => (int) $year,
                     'month' => 12,
                     'day'   => (int) $r_days,
                     'wday'  => (int) $wday,
                     'today' => (int) $today);
    }
    // }}}
    // {{{ convert_to_days
    /**
     * Method of conversion, used to convert a date to jalali days.
     * all days are calculated from UTC Epoch.
     */
    public static function convert_to_days($year, $month, $day)
    {
        $n_years = $year - self::JCAL_BASE_CAL_YEAR;

        $today    = 0;
        $fu_days  = 0;
        $redirect = 0.0;
        $c_flag   = 0.0;

        for ($i=1; $i < $month; $i++) {
            $today += self::$jcal_jalali_month_len[$i-1];
        }

        $today += $day;

        $redirect  = $c_flag = ($n_years * self::JCAL_YEAR_LEND) + ($today - 287.0);
        $c_flag   -= floor($c_flag);

        if ($c_flag >= 0.0) {

            if ($c_flag < 0.75) {
                $fu_days = floor($redirect);
            } else {
                $fu_days = ceil($redirect);
            }

        } else {

            if (abs($c_flag) < 0.5) {
                $fu_days = floor($redirect);
            } else {
                $fu_days = floor($redirect);
            }
        }

        return self::is_jleap($year-1) ? $fu_days + 1 : $fu_days;
    }
    // }}}
    // {{{ calc_current
    /**
     * calc_current() is a method to get the current date. It returns an array
     * with five element which are Year, Month, Day (of the month), Wday (day of week),
     * Today (day of year).
     */
    public static function calc_current()
    {
        $n_seconds = time();

        /*
         * This section requires optimization since time-difference
         * calculation is static at the moment.
         * Assuming IRST +3:30.
         */

        $timezone = new DateTimeZone(Kohana::config('locale.timezone'));
        $time     = new DateTime("now", $timezone);

        $n_seconds += $timezone ->getOffset($time);
        $n_days     = (int) ($n_seconds / self::JCAL_DAY_LEN);

        return self::convert_to_jalali($n_days);
    }
    // }}}
    // {{{ gregorian_to_jalali
    /**
     * Converts gregorian to jalali date.
     */
    public static function gregorian_to_jalali($timestamp)
    {
        /*
         * This section requires optimization since time-difference
         * calculation is static at the moment.
         * Assuming IRST +3:30.
         */

        $timezone = new DateTimeZone(Kohana::config('locale.timezone'));
        $time     = new DateTime("now", $timezone);

        $timestamp += $timezone ->getOffset($time);
        $n_days     = (int) ($timestamp / self::JCAL_DAY_LEN);

        return self::convert_to_jalali($n_days);
    }
    // }}}
    // {{{ jalali_to_gregorian
    /**
     * Converts jalali to gregorian date.
     */
    public static function jalali_to_gregorian($year, $month, $day)
    {
        return (self::convert_to_days($year, $month, $day) * self::JCAL_DAY_LEN);
    }
    // }}}
    // {{{ get_week_day
    /**
     * gets week day, for a certain year, month, day.
     */
    public static function get_week_day($dyear, $dmonth, $dday)
    {
        $fu_days = self::convert_to_days($dyear, $dmonth, $dday);
        $jd      = self::convert_to_jalali($fu_days);

        return $jd['wday'];
    }
    // }}}
    // {{{ get_year_day
    /**
     * gets day number in year, for a certain year, month, day.
     */
    public static function get_year_day($dyear, $dmonth, $dday)
    {
        $fu_days = self::convert_to_days($dyear, $dmonth, $dday);
        $jd      = self::convert_to_jalali($fu_days);

        return $jd['today'];
    }
    // }}}
    // {{{ get_year_week
    /**
     * gets week number, for a certain year, month, day.
     */
    public static function get_year_week($dyear, $dmonth, $dday)
    {
        $fu_days = self::convert_to_days($dyear, $dmonth, $dday);
        $jd      = self::convert_to_jalali($fu_days);

        return ((int) ceil(($jd['today'] + $jd['wday']) / 7));
    }
    // }}}
    // {{{ fuzzy
    public function fuzzy($date, $from = Null, $level = Null)
    {
        if (!$from) {
            $from = time();
        }
        if ($date > $from) {
            $diff = $date - $from;
        } else {
            $diff = $from - $date;
        }

        $units['century'] =  2903040000;
        $units['year']    = 29030400;
        $units['month']   = 2419200;
        $units['week']    = 604800;
        $units['day']     = 86400;
        $units['hour']    = 3600;
        $units['minute']  = 60;

        $names['century'] = array(_("century"), _("centuries"));
        $names['year']    = array(_("year"), _("years"));
        $names['month']   = array(_("month"), _("months"));
        $names['week']    = array(_("week"), _("weeks"));
        $names['day']     = array(_("day"), _("days"));
        $names['hour']    = array(_("hour"), _("hours"));
        $names['minute']  = array(_("minute"), _("minutes"));

        foreach($units as $name => $value) {
            $num  = (int) ($diff / $value);
            if ($num > 0) {
                $diff = $diff % $value;
                $result[$name] = $num;
            }
        }

        if (!$level) {
            $levels    = Kohana::config('date.fuzzy.levels');
            $main_unit = current(array_keys($result));
            $level     = $levels[$main_unit];
        }

        $output    = '';
        $deep      = 0;
        foreach($result as $name => $num) {
            if ($deep > $level) {
                continue;
            }
            if ($num === 1) {
                $title = $names[$name][0];
            } else {
                $title = $names[$name][1];
            }
            if ($output) {
                $output .= ' '._("and").' ';
            }
            $output .= $num .' '.$title;
            $deep++;
        }

        return $output;
    }
    // }}}
    // {{{ get_type
    public static function get_type($name)
    {
        $lang  = Kohana::config('locale.lang') == "fa";
        $types = Input::instance()->Post('type_'.$name, ($lang == 'fa') ? 'jalali' : 'gregorian');
        return $types;
    }
    // }}}
    // {{{ get_time
    public static function get_time($name, $values = Null, $types = Null)
    {
        empty($values) AND $values = Input::instance()->Post($name);
        empty($types) AND $types = Input::instance()->Post('type_'.$name);

        // Are dates comma separated
        (is_string($values) && !empty($values) && (strpos($values, ',') !== False)) AND $values = explode(',', trim($values, ','));

        if ($values === Null) {
            return Null;
        }

        $lang = Kohana::config('locale.lang') == "fa";

        if (!is_array($values)) {

            if (!$types) {
                $types = ($lang == 'fa') ? 'jalali' : 'gregorian';
            }

            return date::strtotime($values, $types);

        } else {

            $dates = Array();

            foreach ($values as $key => $value) {

                if (!isset($types[$key])) {
                    $types[$key] = ($lang == 'fa') ? 'jalali' : 'gregorian';
                }

                $dates[] = date::strtotime($value, $types[$key]);
            }

            return $dates;
        }
    }
    // }}}
    // {{{ strtotime
    public static function strtotime($str, $type=Null)
    {
        if (!$str) {
            return 0;
        }
        $array = explode('/', $str);
        if (count($array)!=3) {
            return 0;
        }
        if (!$type) {
            $lang   = Kohana::config('locale.lang') == "fa";
            $type   = $lang == 'fa' ? 'jalali' : 'gregorian';
        }

        $timezone = new DateTimeZone(Kohana::config('locale.timezone'));
        $time     = new DateTime("now", $timezone);

        if ($type == "jalali") {
            $timestamp = date::jalali_to_gregorian($array[0], $array[1], $array[2]) - ($timezone ->getOffset($time));
        } else {
            $timestamp = mktime(0, 0, 0, (int) $array[1], (int) $array[2], (int) $array[0]);
        }

        return $timestamp;
    }
    // }}}
    // {{{ timetostr
    public static function timetostr($timestamp, $format = 'y/m/d', $do_localization = true)
    {
            if ($do_localization && Kohana::config('locale.lang') == "fa") {
                $date = date::gregorian_to_jalali($timestamp);
                $str  = $date['year'].'/'.
                        (((int) $date['month']) < 10 ? '0' . $date['month'] : $date['month']).'/'.
                        (((int) $date['day']) < 10 ? '0' . $date['day'] : $date['day']);
            } else {
                $str  = date($format, $timestamp);
            }
            return $str;
    }
    // }}}
}
