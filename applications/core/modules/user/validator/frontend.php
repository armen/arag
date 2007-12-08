<?php

/*
 * Error messages
 */

$validator['error_messages'] = Array (
                                       'required'         => _("%s is required"),
                                       'valid_email'      => _("Please entere a valid email address"),
                                       '_check_user_name' => _("This %s is reserved or not available"),
                                       'matches'          => _("%ss do not match"),
                                       'alpha_dash'       => _("%s can contain only alpha-numeric characters, underscores or dashes"),
                                       'alpha'            => _("%s can contain only alpha characters")
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

/*
 * Validation of registration method
 */

$validator['registration']['write']['rules'] = Array (
                                                      'username'   => array(_("Username"), 'trim|required|alpha_dash|callback__check_user_name|min_length[4]'),
                                                      'password'   => array(_("Password"), 'trim|required|matches[repassword]|min_length[4]'),
                                                      'repassword' => array(_("Repassword"), 'trim|required'),
                                                      'name'       => array(_("Name"), 'trim|required|alpha'),
                                                      'lastname'   => array(_("Lastname"), 'trim|required|alpha'),
                                                      'email'      => array(_("Email"), 'trim|required|valid_email|matches[reemail]'),
                                                      'reemail'    => array(_("Remmail"), 'trim|required|valid_email')
                                                     );
?>
