<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Peyman Karimi <zeegco@yahoo.com>                                |
// +-------------------------------------------------------------------------+
// | Smarty {arag_thememanager} function plugin                              |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_thememanager                                              |
// | Purpose: Generating a link ot customised css                            |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_thememanager($params, &$smarty)
{

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            default:
                $smarty->trigger_error("arag_thememanager: Unknown attribute '$_key'");
        }
    }

    $theme_manager     = Model::load('ThemeManager', 'theme_manager');
    $view              = new View('frontend/arag_thememanager');
    $view->makeup_time = $theme_manager->getMakeupTime();

    return $view->render();
}
