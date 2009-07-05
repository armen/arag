<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Peyman Karimi <zeegco@yahoo.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = _("Category Manager");

        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Category Manager"));
        $this->global_tabs->addItem(_("Action"), 'category_manager/backend');
        $this->global_tabs->addItem(_("Delete Confirmation"), 'category_manager/backend/confirm_delete/%key%', 'category_manager/backend');
    }
    // }}}
    // {{{ confirm_delete_read
    public function confirm_delete_read($key = null)
    {
        $this->global_tabs->setParameter('key', $key);

        $yes_uri     = $this->session->get('category_manager.'.$key.'.yes_uri');
        $no_uri      = $this->session->get('category_manager.'.$key.'.no_uri');
        $entity_name = $this->session->get('category_manager.'.$key.'.entity_name');

        if (!$yes_uri || !$no_uri || !$entity_name) {
            $this->_invalid_request();
        }

        $this->layout->content              = new View('backend/confirm_delete');
        $this->layout->content->yes_uri     = $yes_uri;
        $this->layout->content->no_uri      = $no_uri;
        $this->layout->content->entity_name = $entity_name;
    }
    // }}}
}
