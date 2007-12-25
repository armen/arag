<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Messages_Controller extends Controller
{
    // {{{ invalid_request
    public function invalid_request()
    {
        $this->layout->content = new View('messages/invalid_request');

        $this->layout->content->page_title = _("Invalid Request!");
        $this->layout->content->uri        = $this->session->get('_invalid_request_uri');
    }
    // }}}
    // {{{ not_authorized
    public function not_authorized()
    {
        $this->layout->content = new View('messages/not_authorized');

        $this->layout->content->page_title = _("Not Authorized!");
    }
    // }}}
    // {{{ page_not_found
    public function page_not_found()
    {
        $this->layout->content = new View('messages/page_not_found');

        $this->layout->content->page_title = _("Page Not Found!");        
    }
    // }}}
}

?>
