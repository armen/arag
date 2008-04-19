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
    $width    = '150';
    $height   = '50';
    $value    = '';
    $language = config::item('locale.lang');
    
    if (Config::item('captcha.width') != Null) {
        $width = Config::item('captcha.width');
    }

    if (Config::item('captcha.height') != Null) {
        $height = Config::item('captcha.height');
    }

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'value':
            case 'width':
            case 'height':
                $$_key = $_val;
                break;
                
            default:
                $smarty->trigger_error("arag_captcha: Unknown attribute '$_key'");
        }
    }

    $time = time();
    $captcha_source = url::base().$language.'/core/frontend/captcha/render/'.$time;

    $content = '<div><img src="'.$captcha_source.'" alt="captcha" width="'.$width.'" height="'.$height.'" /><div>';
    $content .= '<div><input name="captcha" value="" type="text" /></div>';

    return $content;
}

?>
