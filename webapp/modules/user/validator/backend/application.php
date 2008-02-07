<?php

/*
 * Error messages
 */

$validator['error_messages'] = Array (
                                       'required'          => _("%s is required"),
                                       '_check_group_name' => _("This %s is not available"),
                                       '_check_user_name'  => _("This %s is reserved or not available"),
                                       'matches'           => _("%ss do not match"),
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

$validator['new_group']['write']['rules'] = Array ('newgroup' => Array(_("Name for new group"), '=trim|required|callback__check_group_name'));


/*
 * Validation of new_user method
 */

$validator['new_user']['write']['rules'] = Array (
                                                  'username'   => Array(_("Username"), '=trim|required|alpha_dash|callback__check_user_name|min_length[4]'),
                                                  'password'   => Array(_("Password"), '=trim|required|matches[repassword]|min_length[4]'),
                                                  'repassword' => Array(_("Repassword"), '=trim|required'),
                                                  'name'       => Array(_("Name"), '=trim|required|alpha'),
                                                  'lastname'   => Array(_("Lastname"), '=trim|required|alpha'),
                                                  'email'      => Array(_("Email"), '=trim|required|valid_email'),
                                                  'group'      => Array(_("Group"), '=trim|required')
                                                 );
/*
 * Validation of user_profile method
 */

$validator['user_profile']['read']['rules']  = Array (Array(_("Username") => 'required|callback__check_user_name_profile'));
$validator['user_profile']['write']['rules'] = Array (
                                                      'username' => Array(_("Username"), 'callback__check_user_name_profile'),
                                                      'password' => Array(_("Password"), 'matches[repassword]|min_length[4]|callback__check_password'),
                                                      'name'     => Array(_("Name"), '=trim|required|alpha'),
                                                      'lastname' => Array(_("Lastname"), '=trim|required|alpha'),
                                                      'email'    => Array(_("Email"), '=trim|required|valid_email'),
                                                      'group'    => Array(_("Group"), '=trim|required')
                                                     );

/*
 * Validation of delete method
 */

$validator['do_delete']['write']['rules'] = Array('objects' => Array(_("Objects"), 'callback__check_current_deletables'));

?>
