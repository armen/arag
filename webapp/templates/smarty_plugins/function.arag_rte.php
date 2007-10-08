<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// |          Armen Baghumian <armen@OpenSourceClub.org>                     |
// +-------------------------------------------------------------------------+
// | Smarty {arag_rte} function plugin                                    |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_rte                                                    |
// | Purpose: Generating a FCKeditor                                         |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_rte($params, &$smarty)
{
    $toolbar_set = 'Default';     // Default toolbar set
    $width       = '100%';        // Default width
    $height      = '400';         // Default height
    $value       = '';            // Default value

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
    
    $CI      =& get_instance();
    $pub_url =  $CI->config->slash_item('base_url');    
    
    // Detecting language
    $language = 'en';
    switch ($CI->config->item('Arag_i18n_language_name')) {
        case 'fa_IR.utf8': $language = 'fa'; break;
    }
    
    require_once PUBPATH . 'scripts/FCKeditor/fckeditor.php';

    $FCKeditor =& new FCKeditor($name);
    
    $FCKeditor->Config['CustomConfigurationsPath'] = $pub_url . 'scripts/FCKeditor/fckconfig.js';
    $FCKeditor->Config['AutoDetectLanguage']       = False ;
    $FCKeditor->Config['DefaultLanguage']          = $language;
    $FCKeditor->Config['ContentLangDirection']     = $CI->config->item('Arag_i18n_language_direction');
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
