<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Site_Controller extends Multisite_Backend
{
    // {{{ index_any
    public function index_any($page = NULL)
    {
        $multiSite          = new MultiSite_Model;
        $this->applications = new PList_Component('applications');

        if ($page != Null && preg_match('|^page_applications$|', $page)) {
            $name = $this->session->get('user_app_name');
            $dbid = $this->session->get('user_app_dbid');
        } else {
            $name = $this->input->post('name', Null, True);
            $dbid = $this->input->post('dbid', Null, True);
        }

        $this->session->set('user_app_name', $name);
        $this->session->set('user_app_dbid', $dbid);

        $this->applications->setResource($multiSite->getApps($name, $dbid));
        $this->applications->setLimit(Arag_Config::get('limit', 0));
        $this->applications->addColumn('app_name', _("Name"));
        $this->applications->addColumn('default_group', _("Default Group"));
        $this->applications->addColumn('MultiSite.getDate', _("Create Date"), PList_Component::VIRTUAL_COLUMN);
        $this->applications->addColumn('db_name', _("Database Name"));
        $this->applications->addAction($multiSite->getAppUrl('#app_name#'), _("View"), 'view_action', TRUE);

        $ids = $multiSite->getIDs();

        $data = array("name" => $name,
                      "flag" => false,
                      "ids"  => $ids,
                      "dbid" => $dbid);

        $this->layout->content = new View('backend/site', $data);
    }
    // }}}
    // {{{ index_any_error
    public function index_any_error()
    {
        $this->index_any();
    }
    // }}}
}
