<?php
/**
 * @package  Core
 *
 * Sets the default route to "welcome"
 */

$config = array
(
    '_default'        => 'arag/welcome',
    'captcha/default' => 'core/frontend/captcha/default'
);

if (Kohana::config('locale.multi_lingual')) {
    $config['[a-zA-Z]{2}']      = 'arag/welcome';
    $config['[a-zA-Z]{2}/(.*)'] = '$1';
}
