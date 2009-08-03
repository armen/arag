<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Armen Baghumian <armen@OpenSourceClub.org>                     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Status_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = 'Messengers Status';
    }
    // }}}
    // {{{ index
    public function index_any()
    {
        $messengers = new Messengers_Model;

        $this->layout->content             = new View('frontend/status');
        $this->layout->content->messengers = $messengers->getMessengers();
    }
    // }}}
}
