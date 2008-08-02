<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_form}{/arag_form} block plugin                             |
// |                                                                         |
// | Type:    block function                                                 |
// | Name:    arag_form                                                      |
// | Purpose: Generating form with required hidden parameters                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_block_arag_form($params, $content, &$smarty, &$repeat)
{
    if (!$repeat) {

        $method  = 'post';
        $uri     = Null;
        $enctype = Null;
        $id      = Null;
        $class   = Null;
        $style   = Null;

        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'uri':
                case 'method':
                    $$_key = (string)$_val;
                    break;

                case 'id':
                case 'class':
                case 'enctype':
                case 'style':
                    $$_key = " {$_key}=\"" . (string)$_val . "\" ";
                    break;

                default:
                    $smarty->trigger_error("arag_form: unknown attribute '$_key'");
            }
        }

        if (preg_match('!^\w+://!i', $uri)) {
            // there is \w:// at begining of uri
            $action = $uri;
        } else {
            $action = url::site($uri);
        }

        if (Kohana::config('token.enable')) {
            $content = '<input type="hidden" name="arag_token" value="'.Token::generate().'" />'.$content;
        }

        return '<form action="'.$action.'" method="'. $method.'"' . $style . $id . $class . $enctype .'>'.$content.'</form>';
    }
}
