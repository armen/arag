<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// |          Armen Baghumian <armen@OpenSourceClub.org>                     |
// +-------------------------------------------------------------------------+
// | Smarty {arag_rte} function plugin                                       |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_rte                                                       |
// | Purpose: Generating a FCKeditor                                         |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_rte($params, &$smarty)
{
    $toolbar_set      = 'Default';
    $width            = '100%';
    $height           = '300';
    $value            = '';
    $language         = 'en';
    $skin             = 'default';
    $toolbar_expanded = true;

    $pub_url = url::base();

    $language = Config::item('locale.lang');

    if (Config::item('arag.fckeditor_skin') != Null) {
        $skin = Config::item('arag.fckeditor_skin');
    }

    if (Config::item('arag.fckeditor_width') != Null) {
        $width = Config::item('arag.fckeditor_width');
    }

    if (Config::item('arag.fckeditor_height') != Null) {
        $height = Config::item('arag.fckeditor_height');
    }

    if (Config::item('arag.fckeditor_toolbar_set') != Null) {
        $toolbar_set = Config::item('arag.fckeditor_toolbar_set');
    }

    if (Config::item('arag.fckeditor_toolbar_expanded') === False) {
        $toolbar_expanded = Config::item('arag.fckeditor_toolbar_expanded');
    }

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
            case 'value':
            case 'toolbar_set':
            case 'width':
            case 'height':
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("arag_rte: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
       $smarty->trigger_error("arag_rte: missing 'name' attribute");
       return;
    }

    require_once DOCROOT . 'scripts/FCKeditor/fckeditor.php';

    $FCKeditor =& new FCKeditor($name);

    $FCKeditor->Config['CustomConfigurationsPath'] = $pub_url . 'scripts/FCKeditor/fckconfig.js';
    $FCKeditor->Config['SkinPath']                 = $pub_url . 'scripts/FCKeditor/editor/skins/' . $skin . '/';
    $FCKeditor->Config['ToolbarStartExpanded']     = $toolbar_expanded;
    $FCKeditor->Config['AutoDetectLanguage']       = False ;
    $FCKeditor->Config['DefaultLanguage']          = $language;
    $FCKeditor->Config['ContentLangDirection']     = Config::item('locale.languages_direction.'.$language);
    $FCKeditor->Config['DocType']                  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" '.
                                                     '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

    $FCKeditor->BasePath   = $pub_url . 'scripts/FCKeditor/';
    $FCKeditor->Width      = $width;
    $FCKeditor->Height     = $height;
    $FCKeditor->Value      = $value;
    $FCKeditor->ToolbarSet = $toolbar_set;

    return $FCKeditor->CreateHtml();
}

?>
