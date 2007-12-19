<?php

$validator['error_messages'] = Array ();

/*
 * Validation of View method
 */

$validator['view']['rules'] = Array(Array(_("ID"), 'required|numeric|callback__check_entry'));

/*
 * Validation of Post Comment method
 */

$validator['post_comment']['write']['rules'] = Array (
                                                       'name'     => Array (_("Name"), '=trim|valid::standard_text|required'),
                                                       'email'    => Array (_("Email"), '=trim|valid_email'),
                                                       'homepage' => Array (_("Home page"), '=trim|valid_url'),
                                                       'comment'  => Array (_("Comment"), 'required|xss_clean')
                                                     );

?>
