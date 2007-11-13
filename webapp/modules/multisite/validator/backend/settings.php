<?php

/*
 * Error messages
 */

$validator['error_messages'] = Array (
                                       'valid_type'  => _("%s should be numeric"),
                                       'required'    => _("%s is required"),
                                       'valid_email' => _("%s must be a valid email address")
                                     );
/*
 * Validation of Limit Settings
 */

$validator['index']['write']['rules'] = Array('limit' => array(_("Limit"), 'trim|required|numeric'));

/*
 * Validation of Email Settings
 */

$validator['email']['write']['rules'] = Array(
                                              'smtpserver' => array(_("SMTP server"), 'trim|required'),
                                              'sender'     => array(_("Sender's email"), 'trim|required|valid_email'),
                                              'subject'    => array(_("Subject"), 'trim|required'),
                                              'template'   => array(_("Email's template"), 'trim|required'),
                                              'smtpport'   => array(_("SMTP Port"), 'trim|required|numeric')
                                             );

/*
 * Validation of Password Settings
 */

$validator['password']['write']['rules'] = Array('length' => array(_("Password length"), 'trim|required|numeric'));

/*
 * Validation of URI expire time Settings
 */

$validator['expire_time']['write']['rules'] = Array('expire' => array(_("Expire time"), 'trim|required|numeric'));
?>
