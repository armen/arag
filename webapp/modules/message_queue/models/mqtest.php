<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Mqtest_Model extends Model
{
    // {{{ worker
    public function worker($message, $microtime)
    {
        list($micro, $time) = explode(' ', $microtime);
        $date               = format::date($time);
        $time               = time()-$time;

        if (rand(0,1)) {
            sleep(rand(0,30));
            Kohana::log('info', "(failed after ".$time." seconds) A job receieved with ".var_export($message, True).". create time is ".
                                $date." (".$micro.")");
            Kohana::log_save();
            return False;
        }

        Kohana::log('info', "  (done after ".$time." seconds) A job receieved with ".var_export($message, True).". create time is ".
                            $date." (".$micro.")");
        Kohana::log_save();

        // Its too important to return true, so server will set this work as processed
        return True;
    }
    // }}}
}
