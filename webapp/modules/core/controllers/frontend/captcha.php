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
        ob_end_clean();
        Event::clear('system.shutdown');
        Event::clear('system.display');                
    
        $captcha = new Captcha_Core();
        $captcha->set_code($this->_generate_captcha_code(6));
        $captcha->render();
    }
    // }}}
    // {{{ _generate_captcha_code
    public function _generate_captcha_code($len)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        for ($i = 0; $i < $len; $i++)
        {
            $pos = rand(0, strlen($chars)-1);
            $string .= $chars{$pos};
        }
        return $string;
    }
    // }}}
}

?>
