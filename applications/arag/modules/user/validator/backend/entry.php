<?php

$_fields = Array (
                   'id'                  => _("ID"),
                   'subject'             => _("Subject"), 
                   'entry'               => _("Entry Body"),
                   'extended_entry'      => _("Extended Body"),
                   'status'              => '',
                   'allow_comments'      => '',
                   'requires_moderation' => '',
                   // 'category'            => ''
                 );

$_rules = Array (
                  'subject'             => '=trim|required|alpha_numeric',
                  'entry'               => 'required|xss_clean',
                  'extended_entry'      => 'xss_clean',
                  'status'              => 'numeric',
                  'allow_comments'      => 'numeric',
                  'requires_moderation' => 'numeric',
                  // 'category'            => 'numeric'
                );

/*
 * Error messages
 */

$validator['error_messages'] = Array (
                                       'required'   => _("%s is required."), 
                                       'min_length' => _("minimum length of %s is %d"),
                                       'numeric'    => _("%s should be numeric")
                                     );

/*
 * Validation of Post method
 */

$validator['post']['write']['fields'] = $_fields;
$validator['post']['write']['rules']  = $_rules;

/*
 * Validation of Edit method
 */

$validator['edit']['read']['fields'] = Array(1 => 'Id');
$validator['edit']['read']['rules']  = Array(1 => 'required|numeric|callback__check_entry');

$validator['edit']['write']['fields'] = $_fields;
$validator['edit']['write']['rules']  = array_merge($_rules, Array('id' => 'required|numeric'));

/* 
 * Validation of Delete method
 */

$validator['delete']['read']['fields'] = Array(1 => 'Id');
$validator['delete']['read']['rules']  = Array(1 => 'required|numeric|callback__check_entry');

$validator['delete']['write']['fields'] = Array('id' => 'Id');
$validator['delete']['write']['rules']  = Array('id' => 'required|numeric|callback__check_entry');

/*
 * Validation of Preview method 
 */

$validator['preview']['fields'] = Array(1 => 'Id');
$validator['preview']['rules']  = Array(1 => 'required|numeric|callback__check_entry');

?>
