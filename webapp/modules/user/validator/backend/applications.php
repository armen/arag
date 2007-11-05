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
                                      '_check_privilege'  => _("Please enter a valid %s"),
                                      '_check_app_filter' => _("This application filter exists"));

/*
 * Validation of index method
 */

$_index_fields = Array ('name' => _("Name"));

$_index_rules  = Array ('name' => 'trim|required');

$validator['index']['write']['fields'] = $_index_fields;
$validator['index']['write']['rules']  = $_index_rules;

/*
 * Validation of new_group method
 */

$_new_group_fields = Array ('newgroup' => _("Name for new group"));

$_new_group_rules  = Array ('newgroup' => 'trim|required|callback__check_group_name');

$validator['new_group']['write']['fields'] = $_new_group_fields;
$validator['new_group']['write']['rules']  = $_new_group_rules;

/*
 * Validation of new and edit Label method
 */

$_new_label_fields = Array ('newlabel'  => _("Name for new label"),
                            'privilege' => _("Privilege for new label"));

$_new_label_rules  = Array ('newlabel'  => 'trim|required',
                            'privilege' => 'trim|required|callback__check_privilege');

$_new_label_parent_fields = Array ('newlabel'  => _("Name for new label"));

$_new_label_parent_rules  = Array ('newlabel'  => 'trim|required');

$_edit_label_fields = Array ('label'     => _("Label name"),
                             'privilege' => _("Privilege"));

$_edit_label_rules  = Array ('label'     => 'trim|required',
                             'privilege' => 'trim|callback__check_privilege');

$validator['privileges_parents']['write']['fields'] = $_new_label_parent_fields;
$validator['privileges_parents']['write']['rules']  = $_new_label_parent_rules;

$validator['privileges']['write']['fields'] = $_new_label_fields;
$validator['privileges']['write']['rules']  = $_new_label_rules;

$validator['privileges_edit']['write']['fields'] = $_edit_label_fields;
$validator['privileges_edit']['write']['rules']  = $_edit_label_rules;

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
$validator['user_profile']['write']['rules']  = array_merge(array('password'   => 'matches[repassword]|min_length[4]'),
                                                            $_new_user_rules);


/*
 * Validation of add and edit filters method
 */

$validator['filters_edit']['write']['fields'] = array('filter' => 'Filter');
$validator['filters_edit']['write']['rules']  = array('filter' => 'required|callback__check_filter');

$validator['app_filters']['write']['fields'] = array('filter' => 'Filter');
$validator['app_filters']['write']['rules']  = array('filter' => 'required|callback__check_filter');

/*
 * Validation of Settings method
 */

$_settings_fields = Array ('limit' => _("Limit"));

$_settings_rules = Array ('limit' => 'trim|numeric');

$validator['settings']['write']['fields'] = $_settings_fields;
$validator['settings']['write']['rules']  = $_settings_rules;

/*
 * Validation of Add Apps method
 */

$_add_apps_fields = Array ('appname' => _("application Name"));

$_add_apps_rules = Array ('appname'  => 'trim|required|callback__check_app_filter');

$validator['add_apps_filters']['write']['fields'] = $_add_apps_fields;
$validator['add_apps_filters']['write']['rules']  = $_add_apps_rules;

/*
 * validation of read methods
 */

$validator['groups']['read']['fields'] = Array(1 => 'name');
$validator['groups']['read']['rules']  = Array(1 => 'required|callback__check_app');

$validator['default_group']['read']['fields'] = Array(1 => 'name');
$validator['default_group']['read']['rules']  = Array(1 => 'required|callback__check_app');

$validator['new_group']['read']['fields'] = Array(1 => 'name');
$validator['new_group']['read']['rules']  = Array(1 => 'required|callback__check_app');

$validator['users']['read']['fields'] = Array(1 => 'id',
                                              2 => 'appname');
$validator['users']['read']['rules']  = Array(1 => 'required|numeric|callback__check_group',
                                              2 => 'required|callback__check_app');

$validator['new_user']['read']['fields'] = Array(1 => 'name');
$validator['new_user']['read']['rules']  = Array(1 => 'required|callback__check_app');

$validator['user_profile']['read']['fields'] = Array(1 => 'username');
$validator['user_profile']['read']['rules']  = Array(1 => 'required|callback__check_user_name_profile');

$validator['privileges_edit']['read']['fields'] = Array(1 => 'id');
$validator['privileges_edit']['read']['rules']  = Array(1 => 'required|callback__check_label');
?>
