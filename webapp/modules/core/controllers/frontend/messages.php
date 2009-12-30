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
    public function invalid_request_any()
    {
        $this->layout->content = new View('messages/invalid_request');

        $this->layout->page_title = _("Invalid Request!");
        $this->layout->content->uri        = $this->session->get('_invalid_request_uri');
        $this->layout->content->message    = $this->session->get('_invalid_request_message');
    }
    // }}}
    // {{{ invalid_request_json
    public function invalid_request_json_any()
    {
        header("HTTP/1.0 400 Bad Request");

        $this->layout->content = 'Invalid Request!';
    }
    // }}}
    // {{{ not_authorized
    public function not_authorized_any()
    {
        $this->session->keep_flash('not_authorized_redirect_url');

        $show_login = $this->session->get('not_authorized_redirect_url') || !$this->session->get('user.authenticated');
        $this->layout->content = new View('messages/not_authorized', array('show_login' => $show_login));

        $this->layout->page_title = _("Not Authorized!");
    }
    // }}}
    // {{{ not_authorized_json
    public function not_authorized_json_any()
    {
        header("HTTP/1.0 401 Unauthorized");

        $this->layout->content = 'Not Authorized!';
    }
    // }}}
    // {{{ page_not_found
    public function page_not_found_any()
    {
        $this->layout->content = new View('messages/page_not_found');

        $this->layout->page_title = _("Page Not Found!");
    }
    // }}}
    // {{{ page_not_found_json
    public function page_not_found_json_any()
    {
        header("HTTP/1.0 404 Not Found");

        $this->layout->content = 'Page Not Found!';
    }
    // }}}
}
