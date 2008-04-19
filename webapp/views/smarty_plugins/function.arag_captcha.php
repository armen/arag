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
    $width    = Config::item('captcha.width');
    $height   = Config::item('captcha.hight');
    $language = config::item('locale.lang');
    
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'width':
            case 'height':
                $$_key = $_val;
                break;
                
            default:
                $smarty->trigger_error("arag_captcha: Unknown attribute '$_key'");
        }
    }

    $captcha_source = url::base().$language.'/core/frontend/captcha/render/'.time();

    $content  = '<div><img src="'.$captcha_source.'" alt="captcha" width="'.$width.'" height="'.$height.'" /><div>'.
                '<div><input name="captcha" value="" type="text" /></div>';

    return $content;
}

?>
