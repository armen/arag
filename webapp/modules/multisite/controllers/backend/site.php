<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Site_Controller extends Backend_Controller 
{
    // {{{ index
    public function index($page = NULL)
    {
        $this->load->component('PList', 'applications');

        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {        
            $name = $this->session->get('user_app_name');
            $dbid = $this->session->get('user_app_dbid');
        } else {
            $name = $this->input->post('name', True);
            $dbid = $this->input->post('dbid', True);
        }

        $this->session->set('user_app_name', $name);
        $this->session->set('user_app_dbid', $dbid);

        $this->applications->setResource($this->MultiSite->getApps($name, $dbid));
        $this->applications->setLimit(Arag_Config::get('limit', 0));
        $this->applications->addColumn('app_name', _("Name"));
        $this->applications->addColumn('default_group', _("Default Group"));
        $this->applications->addColumn('MultiSite.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->applications->addColumn('db_name', _("Database Name"));
        $this->applications->addAction('#app_name#', _("View"), 'view_action');      
        
        $ids = $this->MultiSite->getIDs();

        $data = array("name" => $name,
                      "flag" => false,
                      "ids"  => $ids,
                      "dbid" => $dbid);

        $this->load->view('backend/site', $data);
    }
    // }}}
    // {{{ index_error
    public function index_error()
    {
        $this->index();
    }
    // }}}
}
?>
