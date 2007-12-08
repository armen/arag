<?php

$_fields = Array (
                   'limit' => _("Limit")
                 );

$_rules = Array (
                  'limit' => 'trim|numeric'
                );

/*
 * Error messages
 */

$validator['error_messages'] = Array (
                                       'numeric'    => _("%s should be numeric"),
                                       'min_length' => _("minimum length of %s is %d")
                                     );

/*
 * Validation of index method
 */

$validator['index']['write']['fields'] = $_fields;
$validator['index']['write']['rules']  = $_rules;

?>
