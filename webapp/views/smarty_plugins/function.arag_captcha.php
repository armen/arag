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
    $length = Kohana::config('captcha.num_chars');
    $id     = Null;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'width':
            case 'height':
            case 'length':
            case 'id':
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("arag_captcha: Unknown attribute '$_key'");
        }
    }

    $captcha = new Captcha();
    $content = '<div>'.$captcha->render().'</div>'.
               '<div style="padding: 5px 0px;"><input name="captcha"'.(isset($id) ? 'id="'.$id.'"' : ' ').'type="text" /></div>';

    return $content;
}

function generate_captcha_code($length)
{
    $chars  = 'ABCEFGHJKLMNPRSTUVWXYZ2356789';
    $string = Null;

    for ($i = 0; $i < $length; $i++) {

        $pos     = rand(0, strlen($chars) - 1);
        $string .= $chars{$pos};
    }

    return $string;
}

?>
