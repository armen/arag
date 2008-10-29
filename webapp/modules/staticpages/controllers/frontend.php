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
    // {{{ view
    public function view_any($id = 0)
    {
        $this->StaticPages = new StaticPages_Model;

        $exist = false;

        if (is_numeric($id)) {
            $exist = $this->StaticPages->checkID($id);
        }

        if ($exist) {

            $row = $this->StaticPages->getPage($id);

            $data = Array('id'      => $row['id'],
                          'subject' => $row['subject'],
                          'page'    => $row['page']);

            $this->layout->content = new View('frontend/preview', $data);

        } else {
            url::redirect('staticpages/frontend/index','refresh');
        }
    }
    // }}}
}
