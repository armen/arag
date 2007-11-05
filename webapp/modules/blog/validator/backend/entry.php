<?php

/*
 * Error messages
 */

$validator['error_messages'] = Array (
                                       'required'      => _("%s is required."), 
                                       'min_length'    => _("minimum length of %s is %d"),
                                       'valid_type'    => _("%s should be numeric"),
                                       'unknown_error' => _("An unknown error occured!")
                                     );

/*
 * Validation of Post method
 */

$validator['post']['write']['rules'] = Array (
                                               'subject'             => Array(_("Subject"), 'trim|required|alpha_numeric'),
                                               'entry'               => Array(_("Entry Body"), 'required|xss_clean'),
                                               'extended_entry'      => Array(_("Extended Body"), 'xss_clean'),
                                               'status'              => Array('', 'numeric'),
                                               // 'allow_comments'      => Array('', 'numeric'),
                                               // 'requires_moderation' => Array('', 'numeric'),
                                               // 'category'            => Array('', 'numeric')
                                             );

/*
 * Validation of Edit method
 */

$validator['edit']['read']['rules']  = Array(Array(_("ID"), 'required|numeric|callback__check_entry'));
$validator['edit']['write']['rules'] = Array ( 
                                               'id'                  => Array(_("ID"), 'required|numeric|callback__check_entry'),
                                               'subject'             => Array(_("Subject"), 'trim|required|alpha_numeric'),
                                               'entry'               => Array(_("Entry Body"), 'required|xss_clean'),
                                               'extended_entry'      => Array(_("Extended Body"), 'xss_clean'),
                                               'status'              => Array('', 'numeric'),
                                               // 'allow_comments'      => Array('', 'numeric'),
                                               // 'requires_moderation' => Array('', 'numeric'),
                                               // 'category'            => Array('', 'numeric')
                                             );
/* 
 * Validation of Delete method
 */

$validator['delete']['read']['rules']  = Array(Array(_("ID"), 'required|numeric|callback__check_entry'));
$validator['delete']['write']['rules'] = Array('id' => Array(_("ID"), 'required|numeric|callback__check_entry'));

/*
 * Validation of Preview method 
 */

$validator['preview']['rules'] = Array(Array(_("ID"), 'required|numeric|callback__check_entry'));

?>
