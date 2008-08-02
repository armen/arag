<?php

if (Kohana::config('core.enable_hooks')) {
    Event::add('system.routing', 'arag_module_hooks');
}

function arag_module_hooks()
{
    $hooks   = Array();
    $modules = Kohana::config('config.hooks');
    $modules = is_array($modules) ? array_merge($modules, Array(Router::$module)) : Array(Router::$module);

    $modules_path = $include_paths = Kohana::config('core.modules');
    foreach ($modules as $module) {
        $modules_path[] = MODPATH.$module;
    }

    Kohana::config_set('core.modules', $modules_path);

    foreach ($modules as $module) {
        $hooks = array_unique(array_merge($hooks, Kohana::list_files('modules/'.$module.'/hooks', TRUE)));
    }

    // To validate the filename extension
    $ext = -(strlen(EXT));

    foreach($hooks as $hook) {

        if (substr($hook, $ext) === EXT) {
            // Hook was found, include it
            include_once $hook;

        } else {
            // This should never happen
            Kohana::log('error', 'Hook not found: '.$hook);
        }
    }

    Kohana::config_set('core.modules', $include_paths);
}
