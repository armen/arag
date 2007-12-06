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
                                       '_check_filter'     => _("Please enter a valid %s"),
                                       '_check_privilege'  => _("Please enter a valid %s"),
                                       '_check_app_filter' => _("This application filter exists")
                                      );


/*
 * Validation of index method
 */


$validator['index']['write']['rules'] = Array('name' => array(_("Name"), 'trim|required'));


/*
 * Validation of new_group method
 */

$validator['new_group']['read']['rules']  = Array(array(_("Name") => 'required|callback__check_app'));
$validator['new_group']['write']['rules'] = Array ('newgroup' => array(_("Name for new group"), 'trim|required|callback__check_group_name'));


/*
 * Validation of new parent Label method
 */


$validator['privileges_parents']['write']['rules'] = Array('newlabel'  => array(_("Name for new label"), 'trim|required'));


/*
 * Validation of new Label method
 */


$validator['privileges']['write']['rules'] = Array (
                                                     'newlabel'  => array(_("Name for new label"), 'trim|required'),
                                                     'privilege' => array(_("Privilege for new label"),'trim|required|callback__check_privilege')
                                                    );


/*
 * Validation of edit Label method
 */

$validator['privileges_edit']['read']['rules']  = Array(array(_("Label") => 'required|callback__check_label'));
$validator['privileges_edit']['write']['rules'] = Array (
                                                          'label'     => array(_("Label name"), 'trim|required'),
                                                          'privilege' => array(_("Privilege"), 'trim|callback__check_privilege')
                                                         );


/*
 * Validation of new_user method
 */

$validator['new_user']['read']['rules']  = Array(array(_("Name") => 'required|callback__check_app'));
$validator['new_user']['write']['rules'] = Array (
                                                  'username'   => array(_("Username"), 'trim|required|alpha_dash|callback__check_user_name|min_length[4]'),
                                                  'password'   => array(_("Password"), 'trim|required|matches[repassword]|min_length[4]'),
                                                  'repassword' => array(_("Repassword"), 'trim|required'),
                                                  'name'       => array(_("Name"), 'trim|required|alpha'),
                                                  'lastname'   => array(_("Lastname"), 'trim|required|alpha'),
                                                  'email'      => array(_("Email"), 'trim|required|valid_email'),
                                                  'group'      => array(_("Group"), 'trim|required')
                                                 );

/*
 * Validation of user_profile method
 */

$validator['user_profile']['read']['rules']  = Array(array(_("Username") => 'required|callback__check_user_name_profile_master'));
$validator['user_profile']['write']['rules'] = Array (
                                                      'password'   => array(_("Password"), 'matches[repassword]|min_length[4]'),
                                                      'name'       => array(_("Name"), 'trim|required|alpha'),
                                                      'lastname'   => array(_("Lastname"), 'trim|required|alpha'),
                                                      'email'      => array(_("Email"), 'trim|required|valid_email'),
                                                      'group'      => array(_("Group"), 'trim|required')
                                                     );

/*
 * Validation of add filters method
 */


$validator['app_filters']['write']['rules'] = array('filter' => array(_("Filters"), 'required|callback__check_filter'));


/*
 * Validation of edit filters method
 */


$validator['filters_edit']['write']['rules'] = array('filter' => array(_("Filters"), 'required|callback__check_filter'));


/*
 * Validation of Settings method
 */


$validator['settings']['write']['rules'] = Array('limit' => array(_("Limit"), 'trim|numeric'));


/*
 * Validation of Add Apps method
 */


$validator['add_apps_filters']['write']['rules'] = Array('appname'  => array(_("application Name"), 'trim|required|callback__check_app_filter'));



/*
 * validation of group method
 */


$validator['groups']['read']['rules'] = Array(array(_("Name") => 'required|callback__check_app'));


/*
 * validation of default_group method
 */


$validator['default_group']['read']['rules'] = Array(array(_("Name") => 'required|callback__check_app'));


/*
 * validation of users method
 */


$validator['users']['read']['rules'] = Array(
                                             array(_("ID") => 'required|numeric|callback__check_group'),
                                             array(_("Appname") => 'required|callback__check_app')
                                            );

/*
 * Validation of Password Settings
 */

$validator['password']['write']['rules'] = Array('length' => array(_("Password length"), 'trim|required|numeric'));


/*
 * Validation of URI expire time Settings
 */

$validator['expire_time']['write']['rules'] = Array('expire' => array(_("Expire time"), 'trim|required|numeric'));

/*
 * Validation of URI user blocking Settings
 */

$validator['user_blocking']['write']['rules'] = Array(
                                                      'block_expire'  => array(_("Blocking expire time"), 'trim|required|numeric'),
                                                      'block_counter' => array(_("Blocking attempts"), 'trim|required|numeric'),
                                                     );
?>
