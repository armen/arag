<?php

$config = Array
(
    'integration'           => True,        // Enable/Disable Smarty integration
    'templates_ext'         => 'tpl',
    'cache_path'            => Kohana::config('arag.cache_path'),
    'global_templates_path' => APPPATH . 'views/',
    'debugging_ctrl'        => False,
    'debugging'             => True,
    'caching'               => False,
    'force_compile'         => False,
    'security'              => True,         // XXX: DO NOT SET TO FALSE, SERIOUS SECURITY RISK IF SET!
    'secure_dirs'           => Array         // Smarty secure directories
    (
        LIBSPATH.'arag/views',
        MODPATH.'core/views/',
        MODPATH.'user/views/frontend/',
        MODPATH.'core/views/',
        MODPATH.'locations/views/',
        MODPATH.'forecast/views/'
    ),
    'if_funcs'              => Array         // We'll allow these functions in if statement
    (
        'array',  'list',     'trim',       'isset', 'empty',
        'sizeof', 'in_array', 'is_array',   'true',  'false',
        'null',   'reset',    'array_keys', 'end',   'count',
        'strpos'
    ),
    'modifier_funcs'        => Array         // We'll allow these modifiers
    (
        'sprintf', 'count', 'urlencode', 'urldecode', 'htmlspecialchars_decode', 'trim', 'current', 'gettext'
    ),

    'post_filters'          => Array         // XXX: After change clear the cache to take effect
    (
    ),
    'output_filters'        => Array
    (
        'trimwhitespace'
    ),
    'pre_filters'           => Array         // XXX: DO NOT REMOVE arag_escape prefilter, SERIOUS SECURITY RISK IF REMOVED!
                                             // XXX: after change clear the cache to take effect
    (
        'arag_escape', 'arag_gettext'
    ),

    // Do not escape these directories and files
    // Prototype:
    //
    //    Array ('|/views/arag_templates/[a-zA-Z_0-9]+\.tpl$|',
    //           '|/themes/[a-zA-Z_0-9]+/display_blocks\.tpl$|',
    //           '|/themes/[a-zA-Z_0-9]+/[a-zA-Z_0-9]+block\.tpl$|',
    //           '|/views/arag_block/[a-zA-Z_0-9]+\.tpl$|',
    //           '|/views/arag_tabbed_block/[a-zA-Z_0-9]+\.tpl$|'),

    'escape_exclude_list'   => Array
    (
    ),
    'plugins_paths'         => Array
    (
        MODPATH.'tabbedblock/views/plugins',
        MODPATH.'user/views/plugins/',
        MODPATH.'locations/views/plugins/',
        MODPATH.'forecast/views/plugins/',
        APPPATH.'views/smarty_plugins/'
    )
);
