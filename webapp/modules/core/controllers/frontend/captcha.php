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
    public function _construct()
    {
        // Dummy, we don't want to render the frontend layout.
    }
    // }}}
    // {{{ render
    public function render($timestamp)
    {
        $captcha_bg   = file_exists(DOCROOT.'images/misc/captcha.jpg') ? DOCROOT.'images/misc/captcha.jpg' : '';
        $captcha_code = $this->_generate_captcha_code(6);

        $captcha_config = array('background_image' => $captcha_bg);

        $captcha = new Captcha_Core($captcha_config);
        $captcha->set_code($captcha_code);
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
