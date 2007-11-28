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
 * Validation of Email Settings
 */

$validator['email']['write']['rules'] = Array(
                                              'smtpserver' => array(_("SMTP server"), 'trim|required'),
                                              'sender'     => array(_("Sender's email"), 'trim|required|valid_email'),
                                              'subject'    => array(_("Subject"), 'trim|required'),
                                              'template'   => array(_("Email's template"), 'trim|required'),
                                              'smtpport'   => array(_("SMTP Port"), 'trim|required|numeric')
                                             );
?>
