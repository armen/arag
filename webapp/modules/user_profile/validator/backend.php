<?php
/*
 * Error messages
 */


$validator['error_messages'] = Array (
                                       'required'            => _("%s is required"),
                                       'matches'             => _("%ss do not match"),
                                       'valid_type'          => _("%s should be numeric"),
                                       'min_length'          => _("%s should be at least %d characters"),
                                       'max_length'          => _("%s should be maximum %d characters"),
                                       'exact_length'        => _("%s should be exactly %d characters"),
                                       '_check_old_password' => _("Please enter correct %s")
                                      );

/*
 * Validation of index method
 */

$validator['index']['write']['rules'] = Array (
                                                'phone'       => array(_("Phone"), 'required|numeric|max_length[8]'),
                                                'cellphone'   => array(_("Cellphone"), 'numeric|exact_length[11]'),
                                                'address'     => array(_("Address"), 'required'),
                                                'city'        => array(_("City"), 'required'),
                                                'province'    => array(_("Province"), 'required'),
                                                'postal_code' => array(_("Postal Code"), 'numeric|min_length[5]|max_length[10]')
                                              );

/*
 * Validation of password method
 */

$validator['password']['write']['rules'] = Array (
                                                  'oldpassword'   => array(_("Old Password"), 'required|callback__check_old_password'),
                                                  'newpassword'   => array(_("Password"), 'required|matches[renewpassword]|min_length[4]'),
                                                  'renewpassword' => array(_("Re-Password"), 'required')
                                                 );
?>
