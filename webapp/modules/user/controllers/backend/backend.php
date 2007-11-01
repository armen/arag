<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend extends Arag_Controller 
{
    // {{{ Constructor
    function Backend()
    {
        parent::Arag_Controller();

        // Load the models
        $this->load->model('ApplicationsModel');        
        $this->load->model('GroupsModel');        
        $this->load->model('UsersModel');        
        $this->load->model('FiltersModel');
        $this->load->model('PrivilegesModel');

        // Load URL helper
        $this->load->helper('url');

        // Default page title
        $this->load->vars(Array('page_title' => 'User Management'));

        // load Clobal Tabs
        $this->load->component('TabbedBlock', 'global_tabs'); 

        // Backend decorator
        $this->load->decorator('backend/decorator');
    }
    // }}}
    // {{{ _create_users_plist
    function _create_users_plist($id, $appname = NULL, $groupname = NULL, $user = NULL)
    {
        $this->load->component('PList', 'users');
 
        $this->users->setResource($this->UsersModel->getUsers($id, $appname, $groupname, $user));
        $this->users->setLimit($this->config->item('limit', NULL, 0));
        $this->users->addColumn('appname', _("Application"));
        $this->users->addColumn('group_name', _("Group"));
        $this->users->addColumn('lastname', _("Lastname"));
        $this->users->addColumn('user_name', _("Name"));
        $this->users->addColumn('username', _("Username"));
        $this->users->addColumn('email', _("Email"));
        $this->users->addColumn('ApplicationsModel.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->users->addColumn('ApplicationsModel.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);
        $this->users->addColumn('modified_by', _("Modified By"));
        $this->users->addColumn('created_by', _("Created By"));
        $this->users->addAction("user/backend/applications/user_profile/#username#", _("Edit"), 'edit_action');
        $this->users->addAction("user/backend/applications/delete/user/#username#", _("Delete"), 'delete_action');
        $this->users->addAction("user/backend/applications/delete/user", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username'); 
    }
    // }}}
    // {{{ settings_read
    function settings_read()
    {   
        $saved = NULL;
        if ($this->session->userdata('settings_saved')) {
            $saved = $this->session->userdata('settings_saved');
            $this->session->unset_userdata('settings_saved');
        }

        $data          = Array();
        $data['limit'] = $this->config->item("limit");
        $data['saved'] = $saved;

        $this->load->vars($data);        
        $this->load->view('backend/settings');

    }
    // }}}
    // {{{ settings_write
    function settings_write()
    {
        $this->config->set_item('limit', $this->input->post('limit'));
        
        $this->session->set_userdata('settings_saved', true);

        redirect('user/backend/applications/settings');
    }
    // }}}
    // {{{ settings_write_error
    function settings_write_error()
    {
        $this->settings_read();
    }
    // }}} 
    // {{{ _check_group
    function _check_group($id)
    {
        return $this->GroupsModel->hasGroup(NULL, NULL, $id);
    }
    // }}}
    // {{{ _check_group_name
    function _check_group_name($name)
    {
        $appname = $this->input->post('application');
        return !$this->GroupsModel->hasGroup($name, $appname);
    }
    // }}}
    // {{{ _check_user_name
    function _check_user_name($username)
    {
        return (!preg_match("/^[a-z0-9_.]+_admin$/", strtolower($username)) && !$this->UsersModel->hasUserName($username) && preg_match("/^[a-z][a-z0-9_.]*$/", strtolower($username)));
    }
    // }}}
    // {{{ _check_user_name_profile
    function _check_user_name_profile($username)
    {
        return $this->UsersModel->hasUserName($username);
    }
    // }}}
    // {{{ _check_filter
    function _check_filter($filter)
    {
        return ($filter === '*') || (boolean) preg_match("/^([a-z_]+)(\/([a-z_]+|\*)){1,3}$/", strtolower($filter));
    }
    // }}}
    // {{{ _check_privilege
    function _check_privilege($privilege)
    {
        return (boolean) preg_match("/^([a-z_]+)(\/([a-z_]+|\*)){1,3}$/", strtolower($privilege));
    }
    // }}}
    // {{{ _check_label
    function _check_label($id)
    {
        return $this->PrivilegesModel->hasLabel($id);
    }
    // }}}
    // {{{ _create_privileges_list
    function _create_privileges_list($appname, $parentid = NULL)
    {
        $flagsaved = false;
        if ($this->session->userdata('privileges_add_saved')) {
            $flagsaved = $this->session->userdata('privileges_add_saved');
            $this->session->unset_userdata('privileges_add_saved');
        }
        
        $this->load->component('PList', 'privileges');

        $this->privileges->setResource($this->PrivilegesModel->getFilteredPrivileges($appname, $parentid));
        $this->privileges->setLimit($this->config->item('limit', NULL, 0));
        $this->privileges->addColumn('id', _("ID"), PList::HIDDEN_COLUMN);        
        $this->privileges->addColumn('label', _("Label"));
        $this->privileges->addColumn('modified_by', _("Modified By"));
        $this->privileges->addColumn('created_by', _("Created By"));
        $this->privileges->addColumn('ApplicationsModel.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->privileges->addColumn('ApplicationsModel.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);
        $this->privileges->addColumn('privilege', _("Privilege"));
        $this->privileges->addAction('user/backend/applications/privileges_edit/#id#', _("Edit"), 'edit_action');
        $this->privileges->addAction('user/backend/applications/privileges/#id#', _("View"), 'view_action', 'PrivilegesModel.isParent');
        $this->privileges->addAction('user/backend/applications/privileges_delete/#id#', _("Delete"), 'delete_action');
        $this->privileges->addAction("user/backend/applications/privileges_delete", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->privileges->setGroupActionParameterName('id'); 

        $this->load->vars(array("parentid"  => $parentid,
                                "flagsaved" => $flagsaved));

        $this->load->view('backend/privileges'); 
    }
    // }}}
}

?>
