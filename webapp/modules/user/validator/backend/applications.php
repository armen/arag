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


$_index_rules  = Array ('name' => array(_("Name"), 'trim|required'));

$validator['index']['write']['rules']  = $_index_rules;

/*
 * Validation of new_group method
 */

$_new_group_rules  = Array ('newgroup' => array(_("Name for new group"), 'trim|required|callback__check_group_name'));

$validator['new_group']['write']['rules']  = $_new_group_rules;

/*
 * Validation of new and edit Label method
 */

$_new_label_rules  = Array ('newlabel'  => array(_("Name for new label"), 'trim|required'),
                            'privilege' => array(_("Privilege for new label"),'trim|required|callback__check_privilege'));

$_new_label_parent_rules  = Array ('newlabel'  => array(_("Name for new label"), 'trim|required'));

$_edit_label_rules  = Array ('label'     => array(_("Label name"), 'trim|required'),
                             'privilege' => array(_("Privilege"), 'trim|callback__check_privilege'));

$validator['privileges_parents']['write']['rules']  = $_new_label_parent_rules;

$validator['privileges']['write']['rules']  = $_new_label_rules;

$validator['privileges_edit']['write']['rules']  = $_edit_label_rules;

/*
 * Validation of new_user & user_profile method
 */

$_new_user_rules = Array (array('username'   => _("Username"), 'trim|required|alpha_dash|callback__check_user_name|min_length[4]'),
                          array('password'   => _("Password"), 'trim|required|matches[repassword]|min_length[4]'),
                          array('repassword' => _("Repassword"), 'trim|required'),
                          array('name'       => _("Name"), 'trim|required|alpha'),
                          array('lastname'   => _("Lastname"), 'trim|required|alpha'),
                          array('email'      => _("Email"), 'trim|required|valid_email'));


$validator['new_user']['write']['rules']  = $_new_user_rules;

$_edit_user_rules = Array (array('password'   => _("Password"), 'matches[repassword]|min_length[4]'),
                           array('name'       => _("Name"), 'trim|required|alpha'),
                           array('lastname'   => _("Lastname"), 'trim|required|alpha'),
                           array('email'      => _("Email"), 'trim|required|valid_email'));

$validator['user_profile']['write']['rules']  = $_new_user_rules;


/*
 * Validation of add and edit filters method
 */

$validator['filters_edit']['write']['rules']  = array('filter' => array(_("Filters"), 'required|callback__check_filter'));

$validator['app_filters']['write']['rules']  = array('filter' => array(_("Filters"), 'required|callback__check_filter'));

/*
 * Validation of Settings method
 */

$_settings_rules = Array ('limit' => array(_("Limit"), 'trim|numeric'));

$validator['settings']['write']['rules']  = $_settings_rules;

/*
 * Validation of Add Apps method
 */

$_add_apps_rules = Array ('appname'  => array(_("application Name"), 'trim|required|callback__check_app_filter'));

$validator['add_apps_filters']['write']['rules']  = $_add_apps_rules;

/*
 * validation of read methods
 */

$validator['groups']['read']['rules']  = Array(array(_("Name") => 'required|callback__check_app'));

$validator['default_group']['read']['rules']  = Array(array(_("Name") => 'required|callback__check_app'));

$validator['new_group']['read']['rules']  = Array(array(_("Name") => 'required|callback__check_app'));

$validator['users']['read']['rules']  = Array(array(_("ID") => 'required|numeric|callback__check_group'),
                                              array(_("Appname") => 'required|callback__check_app'));

$validator['new_user']['read']['rules']  = Array(array(_("Name") => 'required|callback__check_app'));

$validator['user_profile']['read']['rules']  = Array(array(_("Username") => 'required|callback__check_user_name_profile'));

$validator['privileges_edit']['read']['rules']  = Array(array(_("Label") => 'required|callback__check_label'));
?>
