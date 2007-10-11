<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// {{{ Setting include paths

$include_path = LIBSPATH . PATH_SEPARATOR;

ini_set('include_path', $include_path . ini_get('include_path'));

// }}}
// {{{ Global paths

$config['Arag_templates_path'] = APPPATH . 'templates/';
$config['Arag_cache_path']     = APPPATH . 'cache/';

// }}}
// {{{ Smarty settings

// Enable/Disable CI Smarty integriation
$config['Arag_smarty_integriation'] = True;

// Extension of tpl files
define ('ARAG_TPL_EXT', '.tpl');

$config['Arag_smarty_debugging_ctrl'] = False;
$config['Arag_smarty_debugging']      = True;
$config['Arag_smarty_caching']        = False;
$config['Arag_smarty_force_compile']  = False;
$config['Arag_smarty_security']       = True;   // XXX: DO NOT SET TO FALSE, SERIOUS SECURITY RISK IF SET!

// Smarty secure directories
$config['Arag_smarty_secure_dirs'] = Array();

// We'll allow these functions in if statement
$config['Arag_smarty_if_funcs'] = Array('array', 'list', 'trim', 'isset', 'empty', 'count',
                                           'sizeof','in_array', 'is_array', 'true', 'false',
                                           'null', 'reset', 'array_keys', 'end');

// We'll allow these modifiers
$config['Arag_smarty_modifier_funcs'] = Array('sprintf', 'count');

// Smarty filters (After change clear the cache to take effect - only pre/post filter)
$config['Arag_smarty_post_filters']   = Array();
$config['Arag_smarty_output_filters'] = Array('trimwhitespace');
$config['Arag_smarty_pre_filters']    = Array('arag_escape', 'arag_gettext');  // XXX: DO NOT REMOVE arag_escape prefilter, 
                                                                                        //      SERIOUS SECURITY RISK IF REMOVED!
// Do not escape these directories and files
// Prototype:
//          
//    Array ('|/templates/arag_templates/[a-zA-Z_0-9]+\.tpl$|',
//           '|/themes/[a-zA-Z_0-9]+/display_blocks\.tpl$|',
//           '|/themes/[a-zA-Z_0-9]+/[a-zA-Z_0-9]+block\.tpl$|',                      
//           '|/templates/arag_block/[a-zA-Z_0-9]+\.tpl$|',
//           '|/templates/arag_tabbed_block/[a-zA-Z_0-9]+\.tpl$|');

$config['Arag_smarty_escape_exclude_list'] = Array();

// }}}
// {{{ Segments

$config['Arag_module_segment']     = 1;
$config['Arag_controller_segment'] = 1;

// }}}
// {{{ I18N

$config['Arag_i18n_language']           = 'en';
$config['Arag_i18n_language_name']      = 'en_US.utf8';
$config['Arag_i18n_language_direction'] = 'ltr';
// $config['Arag_i18n_language']           = 'fa_IR.utf8';
// $config['Arag_i18n_language_name']      = 'fa';
// $config['Arag_i18n_language_direction'] = 'rtl';
$config['Arag_i18n_language_charset']   = 'utf-8';
$config['Arag_i18n_gettext_domain']     = 'messages';
$config['Arag_i18n_gettext_msgsdir']    = 'locale/';

// }}}
// {{{ FCKeditor settings

$config['Arag_fckeditor_skin']             = 'silver';
$config['Arag_fckeditor_width']            = '100%';
$config['Arag_fckeditor_height']           = '300';
$config['Arag_fckeditor_toolbar_set']      = 'Default';
$config['Arag_fckeditor_toolbar_expanded'] = False;

// }}}

?>
