<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend extends Arag_Controller 
{
    // {{{ properties
    var $appname;
    // }}}
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

        // Current Application
        $this->appname = "_master_";
    }
    // }}}
    // {{{ _create_users_plist
    function _create_users_plist($id, $appname = NULL, $groupname = NULL, $user = NULL, $flagappname = true)
    {
        $this->load->component('PList', 'users');
 
        $this->users->setResource($this->UsersModel->getUsers($id, $appname, $groupname, $user, $flagappname));
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
        return ($filter === '*') || $this->_check_privilege($filter);
    }
    // }}}
    // {{{ _check_privilege
    function _check_privilege($privilege)
    {
        if ($this->input->post('parentid') === "0") {
            return true;
        }
        return (boolean) preg_match('/^([a-z_]+)((\/[a-z_]+){0,2}(\/\*))|((\/[a-z_]+){2,3})$/', strtolower(trim($privilege, '/')));
    }
    // }}}
    // {{{ _check_label
    function _check_label($id)
    {
        return $this->PrivilegesModel->hasLabel($id);
    }
    // }}}
    // {{{ _check_app_filter
    function _check_app_filter($appname)
    {
        return !$this->FiltersModel->hasApp($appname);
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
    // {{{ _create_groups_list
    function _create_groups_list($appname)
    {
        $this->load->component('PList', 'groups');

        $this->global_tabs->setParameter('name', $appname);

        $this->groups->setResource($this->GroupsModel->getGroups($appname));
        $this->groups->setLimit($this->config->item('limit', NULL, 0));
        $this->groups->addColumn('id', Null, PList::HIDDEN_COLUMN);       
        $this->groups->addColumn('name', _("Name"));
        $this->groups->addColumn('appname', _("Application"));
        $this->groups->addColumn('created_by', _("Created By"));
        $this->groups->addColumn('modified_by', _("Modified By"));
        $this->groups->addColumn('ApplicationsModel.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->groups->addColumn('ApplicationsModel.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);
    }
    // }}}
    // {{{ _default_group
    function _default_group($appname, $flagform = true)
    {
        $flagsaved = false;
        if ($this->session->userdata('default_group_saved')) {
            $flagsaved = $this->session->userdata('default_group_saved');
            $this->session->unset_userdata('default_group_saved');
        }

        $row  = $this->GroupsModel->getAllAppGroups($appname);
        $row2 = $this->GroupsModel->getDefaultGroup($appname);

        $this->global_tabs->setParameter('name', $appname);

        $this->load->vars(array("allgroups"    => $row,
                                "defaultgroup" => $row2[0]['default_group'],
                                "flagsaved"    => $flagsaved,
                                "flagform"     => $flagform,
                                "appname"      => $appname));

        $this->load->view('backend/default_group');
    }
    // }}}
    // {{{ _default_group_write
    function _default_group_write($appname)
    {
        $group   = $this->input->post("dgroup");

        $this->session->set_userdata('default_group_saved', true);
            
        $this->GroupsModel->setGroup($appname, $group);

        $this->default_group_read($appname);
    }
    // }}}
    // {{{ _new_group
    function _new_group($appname, $flagform = true)
    {
        $flagsaved = false;
        if ($this->session->userdata('new_group_saved')) {
            $flagsaved = $this->session->userdata('new_group_saved');
            $this->session->unset_userdata('new_group_saved');
        }

        $this->global_tabs->setParameter('name', $appname); 

        $this->load->vars(array("flagsaved"    => $flagsaved,
                                "flagform"     => $flagform,
                                "appname"      => $appname));

        $this->load->view('backend/new_group.tpl');
    }
    // }}}
    // {{{ _new_group_write
    function _new_group_write($appname)
    {
        $newgroup   = $this->input->post("newgroup", true);
            
        $this->GroupsModel->newGroup($appname, $newgroup);

        $this->session->set_userdata('new_group_saved', true);

        $this->new_group_read($appname, true);
    }
    // }}}
    // {{{ _ckeck_current_group
    function _check_current_group($id)
    {
        $appname = $this->appname;
        return $this->GroupsModel->hasGroup(NULL, $appname, $id);
    }
    // }}}
    // {{{ _new_user
    function _new_user($appname, $flagform = true)
    {
        $flagsaved = false;

        if ($this->session->userdata('new_user_saved')) {
            $flagsaved = true;
            $this->session->unset_userdata('new_user_saved');
        }
        
        $this->global_tabs->setParameter('name', $appname); 
        
        $row  = $this->GroupsModel->getAllAppGroups($appname);
        $row2 = $this->GroupsModel->getDefaultGroup($appname);

        $this->load->vars(array("appname"      => $appname,
                                "flagsaved"    => $flagsaved,
                                "allgroups"    => $row,
                                "defaultgroup" => $row2[0]['default_group'],
                                "flagform"     => $flagform));

        $this->load->view('backend/new_user.tpl');       
    }
    // }}}
    // {{{ _new_user_write
    function _new_user_write($appname)
    {
        $email     = $this->input->post('email', true);
        $name      = strtolower($this->input->post('name', true));
        $lastname  = $this->input->post('lastname', true);
        $groupname = $this->input->post('group', true);
        $username  = $this->input->post('username', true);
        $password  = $this->input->post('password', true);
        
        $this->UsersModel->createUser($appname, $email, $name, $lastname, $groupname, $username, $password);

        $this->session->set_userdata('new_user_saved', true);

        $this->new_user_read($appname);
        
    }
    // }}}
    // {{{ _user_profile
    function _user_profile($username, $flagform = true)
    {
        $flagsaved = false;

        if ($this->session->userdata('edit_user_saved')) {
            $flagsaved = true;
            $this->session->unset_userdata('edit_user_saved');
        }

        $userprofile = $this->UsersModel->getUserProfile($username);
        $group       = $this->GroupsModel->getGroup($userprofile['group_id']);
        $row         = $this->GroupsModel->getAllAppGroups($group['appname']);

        $this->load->vars(array("appname"      => $group['appname'],
                                "flagsaved"    => $flagsaved,
                                "allgroups"    => $row,
                                "defaultgroup" => $group['name'],
                                "username"     => $userprofile['username'],
                                "name"         => $userprofile['name'],
                                "lastname"     => $userprofile['lastname'],
                                "email"        => $userprofile['email'],
                                "blocked"      => $userprofile['blocked'],
                                "flagform"     => $flagform));

        $this->load->view('backend/user_profile.tpl');
    }
    // }}}
    // {{{ _user_profile_write
    function _user_profile_write($appname)
    {
        $email     = $this->input->post('email', true);
        $name      = $this->input->post('name', true);
        $lastname  = $this->input->post('lastname', true);
        $groupname = $this->input->post('group', true);
        $username  = $this->input->post('username', true);
        $password  = $this->input->post('password', true);
        $blocked   = $this->input->post('blocked', true);

        $this->UsersModel->editUser($appname, $email, $name, $lastname, $groupname, $username, $password, $blocked);

        $this->session->set_userdata('edit_user_saved', true);

        $this->user_profile_read($username);   
    }
    // }}}
    // {{{ user_profile_write_error
    function user_profile_write_error()
    {
        $username = $this->input->post('username');
        $this->user_profile_read($username);
    }
    // }}}
    // {{{ _privileges_edit_read
    function _privileges_edit_read($id, $appname, $flagform = true)
    {

        $flagsaved = false;
        if ($this->session->userdata('group_privileges_edit_saved')) {
            $flagsaved = $this->session->userdata('group_privileges_edit_saved');
            $this->session->unset_userdata('group_privileges_edit_saved');
        }

        $parents         = $this->PrivilegesModel->getFilteredPrivileges($appname, "0");
        $subpris         = $this->PrivilegesModel->getAppPrivileges($appname);
        $allselected     = $this->PrivilegesModel->getPrivileges($id, true);
        $selected        = $this->PrivilegesModel->getSelectedPrivileges($subpris, $allselected);
        $selectedparents = $this->PrivilegesModel->getSelectedParents($selected, $parents);

        $this->load->vars(array('parent_privileges' => $selectedparents,
                                'sub_privileges'    => $selected,
                                'id'                => $id,
                                'appname'           => $appname,
                                'flagsaved'         => $flagsaved,
                                'flagform'          => $flagform));

        $this->load->view('backend/group_privileges');
    }
    // }}}
    // {{{ _privileges_edit_write
    function _privileges_edit_write($appname)
    {
        $ids     = $this->input->post('privileges');
        $groupid = $this->input->post('id');
        
        $this->PrivilegesModel->editPrivileges($ids, $groupid, $appname);

        $this->session->set_userdata('group_privileges_edit_saved', true);

        $this->group_privileges_edit_read($groupid, $appname);
    }
    // }}}
}
?>
