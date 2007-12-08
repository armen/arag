<?php defined('SYSPATH') or die('No direct script access.');

$config = Array
(
    // {{{ Global paths

    'cache_path'     => APPPATH . 'cache/',

    // }}}
    // {{{ i18n

    // 'i18n_language'           => 'fa_IR.utf8',
    // 'i18n_language_name'      => 'fa',
    // 'i18n_language_direction' => 'rtl',
    'i18n_language'           => 'en',
    'i18n_language_name'      => 'en_US.utf8',
    'i18n_language_direction' => 'ltr',
    'i18n_language_charset'   => 'utf-8',
    'i18n_gettext_domain'     => 'messages',
    'i18n_gettext_msgsdir'    => 'locale/',

    // }}}
    // {{{ FCKeditor settings

    'fckeditor_skin'             => 'silver',
    'fckeditor_width'            => '100%',
    'fckeditor_height'           => '300',
    'fckeditor_toolbar_set'      => 'Default',
    'fckeditor_toolbar_expanded' => False,

    // }}}
);
