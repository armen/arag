<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class PollManager_Frontend extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = _("Poll Manager");

        $this->validation->message('numeric', _("%s is not numeric"));
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('valid_captcha', _("%s mismatches by text that you typed!"));
        $this->validation->message('_has_poll', _("Invalid poll id."));
        $this->validation->message('_has_choice', _("Invalid choice id."));
        $this->validation->message('_vote_before', _("You vote before for this poll."));
    }
    // }}}
}
