<?php

/*
 * Error messages
 */

$validator['error_messages'] = Array (
                                       'valid_type'  => _("%s should be numeric"),
                                       'required'    => _("%s is required")
                                     );
/*
 * Validation of Limit Settings
 */

$validator['index']['write']['rules'] = Array('limit' => array(_("Limit"), 'trim|required|numeric'));

/*
 * Validation of URI user blocking Settings
 */

$validator['user_blocking']['write']['rules'] = Array(
                                                      'block_expire'  => array(_("Blocking expire time"), 'trim|required|numeric'),
                                                      'block_counter' => array(_("Blocking attempts"), 'trim|required|numeric'),
                                                     );
?>
