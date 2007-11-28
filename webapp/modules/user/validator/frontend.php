<?php

/*
 * Error messages
 */

$validator['error_messages'] = Array (
                                       'required'    => _("%s is required"),
                                       'valid_email' => _("Please entere a valid email address")
                                     );

/*
 * Validation of login method
 */

$validator['login']['write']['rules']  = array(
                                               'username' => array(_("Username"), 'trim|required'),
                                               'password' => array(_("Password"), 'trim|required'),
                                              );

/*
 * Validation of forget_password method
 */

$validator['forget_password']['write']['rules']  = array(
                                                         'username' => array(_("Username"), 'trim|required'),
                                                         'email'    => array(_("Email"), 'trim|required|valid_email')
                                                        );
/*
 * Validation of change_password method
 */

$validator['change_password']['write']['rules']  = array(
                                                         'username' => array(_("Username"), 'trim|required'),
                                                         'email'    => array(_("Email"), 'trim|required|valid_email')
                                                        );

/*
 * Validation of remove method
 */

$validator['remove']['write']['rules']  = array(
                                                'username' => array(_("Username"), 'trim|required'),
                                                'email'    => array(_("Email"), 'trim|required|valid_email')
                                               );
?>
