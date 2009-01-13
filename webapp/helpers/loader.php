<?php

// {{{ Loader
class Loader {
    // {{{ load
    function Load($name, $parameters = Array())
    {
        $modules = Model::load('Modules', 'core');
        foreach($modules->getModules() as $module) {
            $path = MODPATH.$module['module'].'/'.$name.EXT;

            if (file_exists($path)) {
                include($path); //include_once might cause problems here.
            }
        }
    }
    // }}}
}
// }}}
