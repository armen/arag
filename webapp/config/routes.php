<?php
/**
 * Route Configuration
 * -----------------------------------------------------------------------------
 * Supported shortcuts are:
 *
 *   :any - matches any non-blank string
 *   :num - matches any number
 *
 * User Guide: http://kohanaphp.com/user_guide/en/libraries/database.html
 *
 * @param string   _allowed   Permitted URI characters
 * @param string   _default   Default route when no URI segments are found
 */

$config = array
(
    '_default' => 'arag/welcome',
);

if (Kohana::config('locale.multi_lingual')) {
    $config['[a-zA-Z]{2}']      = 'arag/welcome';
    $config['[a-zA-Z]{2}/(.*)'] = '$1';
}
