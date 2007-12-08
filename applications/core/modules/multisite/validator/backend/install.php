<?php

/*
 * Error messages
 */


$validator['error_messages'] = Array (
                                       'required'          => _("%s is required"),
                                       'matches'           => _("%ss do not match"),
                                       'alpha_dash'        => _("%s can contain only alpha-numeric characters, underscores or dashes"),
                                       'valid_email'       => _("Please enter a valid email address"),
                                       '_check_app'        => _("This application name is not available")
                                      );


/*
 * Validation of index method
 */


$validator['index']['write']['rules'] = Array(
                                              'appname' => array(_("Application name"), 'trim|required|alpha_dash|callback__check_app'),
                                              'email'   => array(_("Email"), 'trim|required|matches[reemail]|valid_email'),
                                              'reemail' => array(_("Retype Email"), 'trim|required'),
                                             );
?>
