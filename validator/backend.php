<?php
/*
 * Error messages
 */

$validator['error_messages'] = Array (
                                       'required'   => _("%s is required."),
                                       'numeric'    => _("%s should be numeric")
                                     );

/*
 * Validation of Create method
 */

$validator['create']['write']['rules']          = array(
                                                        'subject' => array(_("Subject"), '=trim|required|regex[/^[-\pL\pN\pZs_]+$/uD]'),
                                                        'page'    => array(_("Page"), 'required|xss_clean')
                                                       );

/*
 * Validation of Edit method
 */

$validator['edit']['write']['rules']          = array(  
                                                      'id'      => array(_("id") , 'required|numeric'),
                                                      'subject' => array(_("Subject"), '=trim|required|regex[/^[-\pL\pN\pZs_]+$/uD]'),
                                                      'page'    => array(_("Page"), 'required|xss_clean')
                                                     );

/*
 * Validation of Settings method
 */

$validator['settings']['write']['rules']          = array('limit' => array(_("Limit"), '=trim|required|numeric'));
?>
