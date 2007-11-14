<?php defined('SYSPATH') or die('No direct script access.');

$config = Array
(
    'integration'           => True,        // Enable/Disable Smarty integration
    'templates_ext'         => '.tpl',
    'cache_path'            => Config::item('arag.cache_path'),
    'public_templates_path' => APPPATH . 'templates/',
    'debugging_ctrl'        => False,
    'debugging'             => True,
    'caching'               => False,
    'force_compile'         => False,
    'security'              => True,         // XXX: DO NOT SET TO FALSE, SERIOUS SECURITY RISK IF SET!
    'secure_dirs'           => Array         // Smarty secure directories
    (
    ),    
    'if_funcs'              => Array         // We'll allow these functions in if statement
    (
        'array',  'list',     'trim',       'isset', 'empty', 
        'sizeof', 'in_array', 'is_array',   'true',  'false',
        'null',   'reset',    'array_keys', 'end',   'count'
    ),
    'modifier_funcs'        => Array         // We'll allow these modifiers
    (
        'sprintf', 'count'
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
    //    Array ('|/templates/arag_templates/[a-zA-Z_0-9]+\.tpl$|',
    //           '|/themes/[a-zA-Z_0-9]+/display_blocks\.tpl$|',
    //           '|/themes/[a-zA-Z_0-9]+/[a-zA-Z_0-9]+block\.tpl$|',                      
    //           '|/templates/arag_block/[a-zA-Z_0-9]+\.tpl$|',
    //           '|/templates/arag_tabbed_block/[a-zA-Z_0-9]+\.tpl$|'),

    'escape_exclude_list'   => Array
    (
    ),
);
