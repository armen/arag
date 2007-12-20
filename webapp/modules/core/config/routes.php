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
    '([a-zA-Z]{2}/)?invalid_request' => 'core/frontend/messages/invalid_request',
    '([a-zA-Z]{2}/)?not_authorized'  => 'core/frontend/messages/not_authorized',
    '([a-zA-Z]{2}/)?core/backend'    => 'core/backend/email'
);

?>
