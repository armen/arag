<?php

if (Config::item('hooks.enable')) {
    Event::add('system.routing', 'arag_module_hooks');
}

function arag_module_hooks()
{
    $hooks   = Array();
    $modules = Config::item('config.hooks');
    $modules = is_array($modules) ? array_merge($modules, Array(Router::$module)) : Array(Router::$module);

    $modules_path = $include_paths = Config::item('core.modules');
    foreach ($modules as $module) {
        $modules_path[] = MODPATH.$module;
    }

    Config::set('core.modules', $modules_path);
    
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
            Log::add('error', 'Hook not found: '.$hook);
        }
    }
    
    Config::set('core.modules', $include_paths);
}
