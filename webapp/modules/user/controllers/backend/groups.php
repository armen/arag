<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Groups_Controller extends Backend_Controller 
{   

    // {{{ index_read
    function index_read($appName = "")
    {   
        $search      = false;
        $group       = true;
        $saved       = false;
        $searchagain = false;

        if ($this->session->get("group_saved")) {
            $saved = true;
            $this->session->destroy("group_saved");
        }

        if ($this->session->get("group_search")) {
            $search = true;
            $this->session->destroy("group_search");
        }

        if ($this->session->get("group_group")) {
            $group = false;
            $this->session->destroy("group_group");
        }

        if ($this->session->get("group_searchagain")) {
            $searchagain = true;
            $this->session->destroy("group_searchagain");
        }

        if ($appName == "" || preg_match('|page[a-z_]*:[0-9]*|', $appName)) {
            $this->_appShow(NULL, true, false, false, true);
        } else {
            $this->_appShow($appName, $search, $group, $saved, $searchagain);
        }
    }
    // }}}
    // {{{ index_write
    function index_write()
    {
        $name = $this->input->post('name');
        
        $this->session->set("group_search", true);
        $this->session->set("group_group", true);
        $this->session->set("group_searchagain", true);

        url::redirect('user/backend/groups/index/'.$name);
    }
    // }}}
    // {{{ index_write_error
    function index_write_error()
    {
        $this->_appShow(NULL, true, false, false, true);
    }
    // }}}
    // {{{ appShow
    function _appShow($appName, $flagSearch, $flagDefaultGroup, $flagSaved, $searchagain)
    {
        $this->load->component('PList', 'groups');
       
        $this->groups->setResource($this->UserManagement->getGroups($appName, $flagSearch, $searchagain));
        $this->groups->setLimit(Arag_Config::get('limit', 0));
        $this->groups->addColumn('id', Null, PList::HIDDEN_COLUMN);       
        $this->groups->addColumn('name', _("Name"));
        $this->groups->addColumn('appname', _("Application"));
        $this->groups->addColumn('modified_by', _("Modified By"));
        $this->groups->addColumn('UserManagement.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->groups->addColumn('UserManagement.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);
        $this->groups->addAction('user/backend/users/index/#id#', _("Edit"), 'edit_action');      
                    
        $this->load->vars(array("appname"     => $appName,
                                "flagsearch"  => $flagSearch,
                                "flagdefault" => $flagDefaultGroup,
                                "flagsaved"   => $flagSaved));

        if ($flagDefaultGroup) {

            list($row, $row2) = $this->UserManagement->defaultGroup($appName);

            $this->load->vars(array("allgroups"    => $row,
                                    "defaultgroup" => $row2[0]['default_group']));
        }

        if ($this->session->destroy("group_search")) {
            $this->session->destroy("group_search");
        }

        $this->load->view('backend/groups');
    }
    // }}}
    // {{{ set_group
    function set_group() 
    {
        if ($this->input->post("submit", true)) {

            $appName = $this->input->post("application");
            $group   = $this->input->post("dgroup");
            
            $this->UserManagement->setGroup($appName, $group);

            $this->session->set("group_saved", true);

            url::redirect('user/backend/groups/index/'.$appName);

        } else {
            $this->_invalid_request("user/backend/groups/index");
        }
    }
    // }}}
}
?>
