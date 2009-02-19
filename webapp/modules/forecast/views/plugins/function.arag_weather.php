<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_weather($params, &$smarty)
{
    $forecast = Model::load('Forecast', 'forecast');
    $weather  = $forecast->getWeather($params['location']);
    $location = $forecast->getLocation($params['location']);
    if(isset($params['name'])) {
        $location['name'] = $params['name'];
    }
    $size     = isset($params['size']) ? $params['size'] : 93;
    $smarty->assign('weather', $weather);
    $smarty->assign('size', $size);
    $smarty->assign('location', $location);
    return $smarty->fetch(Arag::find_file('forecast', 'views', 'weather', True, Kohana::config('smarty.templates_ext')));
}

?>