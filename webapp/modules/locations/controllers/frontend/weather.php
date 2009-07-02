<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Weather_Controller extends Controller
{
    // {{{ get_read
    public function get_read($name) //Its a proxy to face cross-domain scripting issue.
    {
        $forecast = Model::load('Forecast', 'forecast');
        die(json_encode($forecast->getWeather($name)));
    }
    // }}}
}
