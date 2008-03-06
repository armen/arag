<?php

if (Config::item('hooks.enable')) {
    Event::add('system.routing', 'arag_module_hooks');
}

function arag_module_hooks()
{
    // All of the hooks are enabled, so we use list_files
    $hooks = array_unique(Kohana::list_files('modules/'.Router::$module.'/hooks', TRUE));

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
}
