<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Applications extends Backend 
{
    // {{{ index
    function index($page = Null)
    {
        $this->load->model('UserManagementModel');

        $this->load->component('PList', 'applications');

        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {        
            $name = $this->session->userdata('user_app_name');
        } else {
            $name = $this->input->post('name', True);
        }

        $this->session->set_userdata('user_app_name', $name);

        $this->applications->setResource($this->UserManagementModel->getApp($name));
        $this->applications->setLimit(1);
        $this->applications->addColumn('name', _("Name"));        
        $this->applications->addColumn('default_group', _("Default Group"));
        $this->applications->addColumn('UserManagementModel.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->applications->addAction('user_management/backend/groups/#name#', _("Edit"), 'edit_action');      
        
        $this->load->vars(array("name" => $name));
        $this->load->view('backend/index'); 
    }
    // }}}
    // {{{ index_error
    function index_error()
    {
        $this->index();
    }
    // }}}
}
?>
