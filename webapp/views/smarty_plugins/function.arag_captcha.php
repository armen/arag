<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author:  Roham Rafii Tehrani<roham.rafii@gmail.com>                     |
// +-------------------------------------------------------------------------+
// | Smarty {arag_captcha} function plugin                                   |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_captcha                                                   |
// | Purpose: Generating a Captcha                                           |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_captcha($params, &$smarty)
{
    $width  = Kohana::config('captcha.width');
    $height = Kohana::config('captcha.height');
    $style  = Kohana::config('captcha.style');
    $length = Kohana::config('captcha.num_chars');
    $code   = Null;
    
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'width':
            case 'height':
            case 'style':
            case 'length':
            case 'code':
                $$_key = $_val;
                break;
                
            default:
                $smarty->trigger_error("arag_captcha: Unknown attribute '$_key'");
        }
    }

    $session = Session::instance();
    $session->set('captcha_code', ($code == Null ? generate_captcha_code(Kohana::config('captcha.num_chars')) : Null));

    $captcha_source = url::site('/core/frontend/captcha/render/'.time());
    $content        = '<div><img src="'.$captcha_source.'" alt="captcha" width="'.$width.'" height="'.$height.'" /></div>'.
                      '<div><input name="captcha" value="" type="text" /></div>';

    return $content;
}

function generate_captcha_code($len)
{
    $chars = 'ABCEFGHJKLMNPRSTUVWXYZ2356789';
    $string = '';
    for ($i = 0; $i < $len; $i++) {
        $pos = rand(0, strlen($chars)-1);
        $string .= $chars{$pos};
    }
    return $string;
}

?>
