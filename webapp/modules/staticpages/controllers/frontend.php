<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend_Controller extends Controller
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = _("Static Pages");
    }
    // }}}
    // {{{ view_read
    public function view_read($id)
    {
        $staticPages = new StaticPages_Model;
        $row         = $staticPages->getPage($id);

        $data = Array('id'      => $row['id'],
                      'subject' => $row['subject'],
                      'page'    => $row['page']);

        $this->layout->page_title = $row['subject'];
        $this->layout->content    = new View('frontend/preview', $data);
    }
    // }}}
    // {{{ view_validate_read
    public function view_validate_read()
    {
        $this->validation->add_rules(0, 'required', array($this, '_exists'));
        return $this->validation->validate();
    }
    // }}}
    // {{{ view_validation_read_error
    public function view_validation_read_error()
    {
        $this->_invalid_request();
    }
    // }}}
    // {{{ _exists
    public function _exists()
    {
        $staticPages = new StaticPages_Model;
        $id          = $this->uri->argument(1);
        return (bool) $staticPages->checkID($id);
    }
    // }}}
}
