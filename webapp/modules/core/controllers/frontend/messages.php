<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Messages extends Arag_Controller
{
    // {{{ invalid_request
    function invalid_request()
    {
        $this->load->helper('url');
        $uri = $this->session->userdata('_invalid_request_uri');
        
        // Replace current decorator with local decorator
        $this->output->set_decorator('decorator.tpl');

        $this->load->vars(Array('page_title' => _("Invalid Request!"),
                                '_site_url'  => site_url($uri)));

        $this->load->view('messages/invalid_request.tpl');
    }
    // }}}
}

?>
