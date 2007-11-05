<?php

/*
 * Error messages
 */

$validator['error_messages'] = Array ('required'          => _("%s is required"),
                                      '_check_group_name' => _("This %s is not available"),
                                      '_check_user_name'  => _("This %s is reserved or not available"),
                                      'matches'           => _("%s do not match"),
                                      'alpha_dash'        => _("%s can contain only alpha-numeric characters, underscores or dashes"),
                                      'alpha'             => _("%s can contain only alpha characters"),
                                      'valid_email'       => _("Please enter a valid email address"),
                                      'min_length'        => _("%s should be at least %d characters"),
                                      '_check_filter'     => _("Please enter a valid %s"),
                                      '_check_privilege'  => _("Please enter a valid %s"));

/*
 * Validation of new_group method
 */

$_new_group_fields = Array ('newgroup' => _("Name for new group"));

$_new_group_rules  = Array ('newgroup' => 'trim|required|callback__check_group_name');

$validator['new_group']['write']['fields'] = $_new_group_fields;
$validator['new_group']['write']['rules']  = $_new_group_rules;


/*
 * Validation of new_user & user_profile method
 */

$_new_user_fields = Array ('username'   => _("Username"),
                           'password'   => _("Password"),
                           'repassword' => _("Repassword"),
                           'name'       => _("Name"),
                           'lastname'   => _("Lastname"),
                           'email'      => _("Email"),
                           'group'      => _("Group ID"));

$_new_user_rules  = Array ('name'       => 'trim|required|alpha',
                           'lastname'   => 'trim|required|alpha',
                           'email'      => 'trim|required|valid_email');

$validator['new_user']['write']['fields'] = $_new_user_fields;
$validator['new_user']['write']['rules']  = array_merge(array('username' => 'trim|required|alpha_dash|callback__check_user_name|min_length[4]'),
                                                       array('password' => 'trim|required|matches[repassword]|min_length[4]', 'repassword' => 'trim|required'),                                                       
                                                       $_new_user_rules);

$validator['user_profile']['write']['fields'] = $_new_user_fields;
$validator['user_profile']['write']['rules']  = array_merge(array('password'   => 'matches[repassword]|min_length[4]'), $_new_user_rules);

/*
 * validation of read methods
 */

$validator['users']['read']['fields'] = Array(1 => 'id');
$validator['users']['read']['rules']  = Array(1 => 'required|numeric|callback__check_current_group');

$validator['user_profile']['read']['fields'] = array(1 => 'id');
$validator['user_profile']['read']['rules']  = array(1 => 'required|callback__check_app_username');

?>
