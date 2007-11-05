<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Messages_Controller extends Controller
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Replace current decorator with local decorator
        $this->load->decorator('decorator');
    }
    // }}}
    // {{{ invalid_request
    function invalid_request()
    {
        $view = $this->load->view('messages/invalid_request');
        $view->page_title = _("Invalid Request!");
        $view->uri        = $this->session->get('_invalid_request_uri');
    }
    // }}}
    // {{{ not_authorized
    function not_authorized()
    {
        $view = $this->load->view('messages/not_authorized');
        $view->page_title = _("Not Authorized!");
    }
    // }}}
}

?>
