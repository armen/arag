<?php
/**
 * @package  Core
 *
 * Default language locale name(s).
 * First item must be a valid i18n directory name, subsequent items are alternative locales
 * for OS's that don't support the first (e.g. Windows). The first valid locale in the array will be used.
 * @see http://php.net/setlocale
 */
$config['language'] = array('en_US', 'English_United States');

/**
 * Default country locale.
 */
$config['country'] = 'USA';

/**
 * Locale timezone. Defaults to use the server timezone.
 * @see http://php.net/timezones
 */
$config['timezone'] = 'Asia/Tehran';

$config['lang']          = 'en';
$config['multi_lingual'] = True;

$config['allowed_locales'] = array
(
    'en' => Array('en_US', 'English_United States'),
    'fa' => Array('fa_IR', 'Farsi')
);

$config['languages_direction'] = array
(
    'en' => 'ltr',
    'fa' => 'rtl'
);

$config['default_country']  = 32;
$config['default_province'] = 7;
$config['default_city']     = 111299;
