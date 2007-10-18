<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Messages extends Arag_Controller
{
    // {{{ Constructor
    function Messages()
    {
        parent::Arag_Controller();

        // Replace current decorator with local decorator
        $this->output->set_decorator('decorator.tpl');

        $this->load->helper('url');
    }
    // }}}
    // {{{ invalid_request
    function invalid_request()
    {
        // Get uri from session
        $uri = $this->session->userdata('_invalid_request_uri');
        
        $this->load->vars(Array('page_title' => _("Invalid Request!"),
                                '_site_url'  => site_url($uri)));
        
        $this->load->view('messages/invalid_request.tpl');
    }
    // }}}
    // {{{ not_authorized
    function not_authorized()
    {
        $this->load->vars(Array('page_title' => _("Not Authorized!"),
                                '_site_url'  => site_url()));
        
        $this->load->view('messages/not_authorized.tpl');        
    }
    // }}}
}

?>
