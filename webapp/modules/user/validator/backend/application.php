<?php

/*
 * Error messages
 */


$validator['error_messages'] = Array (
                                       'required'          => _("%s is required"),
                                       '_check_group_name' => _("This %s is not available"),
                                       '_check_user_name'  => _("This %s is reserved or not available"),
                                       'matches'           => _("%s do not match"),
                                       'alpha_dash'        => _("%s can contain only alpha-numeric characters, underscores or dashes"),
                                       'alpha'             => _("%s can contain only alpha characters"),
                                       'valid_type'        => _("%s should be numeric"),
                                       'valid_email'       => _("Please enter a valid email address"),
                                       'min_length'        => _("%s should be at least %d characters"),
                                       '_check_password'   => _("Please enter correct %s")
                                      );


/*
 * Validation of new_group method
 */

$validator['new_group']['write']['rules'] = Array ('newgroup' => array(_("Name for new group"), 'trim|required|callback__check_group_name'));


/*
 * Validation of new_user method
 */

$validator['new_user']['write']['rules'] = Array (
                                                  'username'   => array(_("Username"), 'trim|required|alpha_dash|callback__check_user_name|min_length[4]'),
                                                  'password'   => array(_("Password"), 'trim|required|matches[repassword]|min_length[4]'),
                                                  'repassword' => array(_("Repassword"), 'trim|required'),
                                                  'name'       => array(_("Name"), 'trim|required|alpha'),
                                                  'lastname'   => array(_("Lastname"), 'trim|required|alpha'),
                                                  'email'      => array(_("Email"), 'trim|required|valid_email')
                                                 );

/*
 * Validation of user_profile method
 */

$validator['user_profile']['read']['rules']  = Array(array(_("Username") => 'required|callback__check_user_name_profile'));
$validator['user_profile']['write']['rules'] = Array (
                                                      'username'    => array(_("Username"), 'callback__check_user_name_profile'),
                                                      'password'    => array(_("Password"), 'matches[repassword]|min_length[4]|callback__check_password'),
                                                      'name'        => array(_("Name"), 'trim|required|alpha'),
                                                      'lastname'    => array(_("Lastname"), 'trim|required|alpha'),
                                                      'email'       => array(_("Email"), 'trim|required|valid_email')
                                                     );

/*
 * Validation of delete method
 */

$validator['do_delete']['write']['rules'] = Array('objects' => array(_("Objects"), 'callback__check_current_deletables'));
?>
