<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class TabController_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        // Dummy, do not call parent cunstructor
    }
    // }}}
    // {{{ index
    public function index_any()
    {
        $uri = isset($_GET['tab']) ? trim(str_replace('.', '/', $_GET['tab']), '/') : Null;

        if (strpos($uri, 'tabcontroller') === False) {
            $GLOBALS['load_scripts_inline'] = True;

            echo Controller::execute($uri, True);

            unset($GLOBALS['load_scripts_inline']);
        }
        exit;
    }
    // }}}
}
