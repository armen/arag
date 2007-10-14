<?php

$_fields = Array (
                   'id'                  => 'Id',
                   'subject'             => 'Subject', 
                   'entry'               => 'Entry Body',
                   'extended_entry'      => 'Extended Body',
                   'status'              => '',
                   'allow_comments'      => '',
                   'requires_moderation' => '',
                   'category'            => ''
                 );

$_rules = Array (
                  'subject'             => 'trim|required|alpha_numeric',
                  'entry'               => 'required|xss_clean',
                  'extended_entry'      => 'xss_clean',
                  'status'              => 'numeric',
                  'allow_comments'      => 'numeric',
                  'requires_moderation' => 'numeric',
                  'category'            => 'numeric'
                );

/*
 * Error messages
 */

$validator['error_messages'] = Array (
                                       'required'   => '%s is required.', 
                                       'min_length' => 'minimum length of %s is %d',
                                       'numeric'    => '%s should be numeric'
                                     );

/*
 * Validation of Post method
 */

$validator['post']['write']['fields']         = $_fields;
$validator['post']['write']['rules']          = $_rules;

/*
 * Validation of Edit method
 */

$validator['edit']['write']['fields']         = $_fields;
$validator['edit']['write']['rules']          = array_merge($_rules, Array('id' => 'required|numeric'));

?>
