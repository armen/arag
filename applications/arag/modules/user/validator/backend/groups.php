<?php

$_fields = Array (
                   'name' => _("Name")
                 );

$_rules = Array (
                  'name' => 'trim|required'
                );

/*
 * Error messages
 */

$validator['error_messages'] = Array ('required'    => _("%s is required"));

/*
 * Validation of index method
 */

$validator['index']['write']['fields'] = $_fields;
$validator['index']['write']['rules']  = $_rules;

?>
