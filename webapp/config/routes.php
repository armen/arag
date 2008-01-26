<?php defined('SYSPATH') or die('No direct script access.');
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
    '_allowed'         => 'a-z 0-9~%.:_-',
    '_default'         => 'arag/welcome',
    '[a-zA-Z]{2}'      => 'arag/welcome',
    '[a-zA-Z]{2}/(.*)' => '$1'
);

?>
