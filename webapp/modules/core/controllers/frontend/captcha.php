<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Roham Rafii Tehrani <roham.rafii@gmail.com>                     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Captcha_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        // Dummy, we don't want to render the frontend layout.
    }
    // }}}
    // {{{ render
    public function render($timestamp)
    {
        $session = Session::instance();
        $code    = $session->get('captcha_code');

        ob_end_clean();
        Event::clear('system.shutdown');
        Event::clear('system.display');                

        $captcha = new Captcha_Core();
        $captcha->set_code($code);
        $captcha->render();
    }
    // }}}
}

?>
