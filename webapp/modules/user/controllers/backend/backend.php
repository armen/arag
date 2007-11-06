<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller 
{
    // {{{ properties
    public $appname;
    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Load the models
        $this->load->model('Applications');        
        $this->load->model('Groups');        
        $this->load->model('Users');        
        $this->load->model('Filters');
        $this->load->model('Privileges');

        // load global Tabs
        $this->load->component('TabbedBlock', 'global_tabs'); 

        // Backend decorator
        $this->load->decorator('backend/decorator');

        // Default page title
        $this->decorator->page_title = 'User Management';

        // Current Application
        $this->appname = "_master_";
    }
    // }}}
    // {{{ _create_users_plist
    protected function _create_users_plist($id, $appname = NULL, $groupname = NULL, $user = NULL, $flagappname = true)
    {
        $this->load->component('PList', 'users');
 
        $this->users->setResource($this->Users->getUsers($id, $appname, $groupname, $user, $flagappname));
        $this->users->setLimit(Arag_Config::get('limit', 0));
        $this->users->addColumn('appname', _("Application"));
        $this->users->addColumn('group_name', _("Group"));
        $this->users->addColumn('lastname', _("Lastname"));
        $this->users->addColumn('user_name', _("Name"));
        $this->users->addColumn('username', _("Username"));
        $this->users->addColumn('email', _("Email"));
        $this->users->addColumn('Applications.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->users->addColumn('Applications.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);
        $this->users->addColumn('modified_by', _("Modified By"));
        $this->users->addColumn('created_by', _("Created By"));
    }
    // }}}
    // {{{ settings_read
    public function settings_read()
    {   
        $saved = NULL;
        if ($this->session->get('settings_saved')) {
            $saved = $this->session->get('settings_saved');
            $this->session->del('settings_saved');
        }

        $data          = Array();
        $data['limit'] = Arag_Config::get("limit");
        $data['saved'] = $saved;

        $this->load->vars($data);        
        $this->load->view('backend/settings');

    }
    // }}}
    // {{{ settings_write
    public function settings_write()
    {
        Arag_Config::set('limit', $this->input->post('limit'));
        
        $this->session->set('settings_saved', true);

        url::redirect('user/backend/applications/settings');
    }
    // }}}
    // {{{ settings_write_error
    public function settings_write_error()
    {
        $this->settings_read();
    }
    // }}} 
    // {{{ _check_group
    protected function _check_group($id)
    {
        return $this->Groups->hasGroup(NULL, NULL, $id);
    }
    // }}}
    // {{{ _check_group_name
    protected function _check_group_name($name)
    {
        $appname = $this->input->post('application');
        return !$this->Groups->hasGroup($name, $appname);
    }
    // }}}
    // {{{ _check_user_name
    protected function _check_user_name($username)
    {
        return (!preg_match("/^[a-z0-9_.]+_admin$/", strtolower($username)) && 
                !$this->Users->hasUserName($username) && preg_match("/^[a-z][a-z0-9_.]*$/", strtolower($username)));
    }
    // }}}
    // {{{ _check_user_name_profile
    protected function _check_user_name_profile($username)
    {
        return $this->Users->hasUserName($username);
    }
    // }}}
    // {{{ _check_filter
    protected function _check_filter($filter)
    {
        return ($filter === '*') || $this->_check_privilege($filter);
    }
    // }}}
    // {{{ _check_privilege
    protected function _check_privilege($privilege)
    {
        if ($this->input->post('parentid') === "0") {
            return true;
        }
        return (boolean) preg_match('/^([a-z_]+)((\/[a-z_]+){0,2}(\/\*))|((\/[a-z_]+){2,3})$/', strtolower(trim($privilege, '/')));
    }
    // }}}
    // {{{ _check_label
    protected function _check_label($id)
    {
        return $this->Privileges->hasLabel($id);
    }
    // }}}
    // {{{ _check_app_filter
    protected function _check_app_filter($appname)
    {
        return !$this->Filters->hasApp($appname);
    }
    // }}}
    // {{{ _create_privileges_list
    protected function _create_privileges_list($appname, $parentid = NULL)
    {
        $flagsaved = false;
        if ($this->session->get('privileges_add_saved')) {
            $flagsaved = $this->session->get('privileges_add_saved');
            $this->session->del('privileges_add_saved');
        }
        
        $this->load->component('PList', 'privileges');

        $this->privileges->setResource($this->Privileges->getFilteredPrivileges($appname, $parentid));
        $this->privileges->setLimit(Arag_Config::get('limit', 0));
        $this->privileges->addColumn('id', _("ID"), PList::HIDDEN_COLUMN);        
        $this->privileges->addColumn('label', _("Label"));
        $this->privileges->addColumn('modified_by', _("Modified By"));
        $this->privileges->addColumn('created_by', _("Created By"));
        $this->privileges->addColumn('Applications.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->privileges->addColumn('Applications.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);
        $this->privileges->addColumn('privilege', _("Privilege"));
        $this->privileges->addAction('user/backend/applications/privileges_edit/#id#', _("Edit"), 'edit_action');
        $this->privileges->addAction('user/backend/applications/privileges/#id#', _("View"), 'view_action', 'Privileges.isParent');
        $this->privileges->addAction('user/backend/applications/privileges_delete/#id#', _("Delete"), 'delete_action');
        $this->privileges->addAction("user/backend/applications/privileges_delete", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->privileges->setGroupActionParameterName('id'); 

        $this->load->vars(array("parentid"  => $parentid,
                                "flagsaved" => $flagsaved));

        $this->load->view('backend/privileges'); 
    }
    // }}}
    // {{{ _create_groups_list
    protected function _create_groups_list($appname)
    {
        $this->load->component('PList', 'groups');

        $this->global_tabs->setParameter('name', $appname);

        $this->groups->setResource($this->Groups->getGroups($appname));
        $this->groups->setLimit(Arag_Config::get('limit', 0));
        $this->groups->addColumn('id', Null, PList::HIDDEN_COLUMN);       
        $this->groups->addColumn('name', _("Name"));
        $this->groups->addColumn('appname', _("Application"));
        $this->groups->addColumn('created_by', _("Created By"));
        $this->groups->addColumn('modified_by', _("Modified By"));
        $this->groups->addColumn('Applications.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->groups->addColumn('Applications.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);
    }
    // }}}
    // {{{ _default_group
    protected function _default_group($appname, $flagform = true)
    {
        $flagsaved = false;
        if ($this->session->get('default_group_saved')) {
            $flagsaved = $this->session->get('default_group_saved');
            $this->session->del('default_group_saved');
        }

        $row  = $this->Groups->getAllAppGroups($appname);
        $row2 = $this->Groups->getDefaultGroup($appname);

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
    protected function _default_group_write($appname)
    {
        $group   = $this->input->post("dgroup");

        $this->session->set('default_group_saved', true);
            
        $this->Groups->setGroup($appname, $group);

        $this->default_group_read($appname);
    }
    // }}}
    // {{{ _new_group
    protected function _new_group($appname, $flagform = true)
    {
        $flagsaved = false;
        if ($this->session->get('new_group_saved')) {
            $flagsaved = $this->session->get('new_group_saved');
            $this->session->del('new_group_saved');
        }

        $this->global_tabs->setParameter('name', $appname); 

        $this->load->vars(array("flagsaved"    => $flagsaved,
                                "flagform"     => $flagform,
                                "appname"      => $appname));

        $this->load->view('backend/new_group.tpl');
    }
    // }}}
    // {{{ _new_group_write
    protected function _new_group_write($appname)
    {
        $newgroup   = $this->input->post("newgroup", true);
            
        $this->Groups->newGroup($appname, $newgroup);

        $this->session->set('new_group_saved', true);

        $this->new_group_read($appname, true);
    }
    // }}}
    // {{{ _ckeck_current_group
    protected function _check_current_group($id)
    {
        $appname = $this->appname;
        return $this->Groups->hasGroup(NULL, $appname, $id);
    }
    // }}}
    // {{{ _new_user
    protected function _new_user($appname, $flagform = true)
    {
        $flagsaved = false;

        if ($this->session->get('new_user_saved')) {
            $flagsaved = true;
            $this->session->del('new_user_saved');
        }
        
        $this->global_tabs->setParameter('name', $appname); 
        
        $row  = $this->Groups->getAllAppGroups($appname);
        $row2 = $this->Groups->getDefaultGroup($appname);

        $this->load->vars(array("appname"      => $appname,
                                "flagsaved"    => $flagsaved,
                                "allgroups"    => $row,
                                "defaultgroup" => $row2[0]['default_group'],
                                "flagform"     => $flagform));

        $this->load->view('backend/new_user.tpl');       
    }
    // }}}
    // {{{ _new_user_write
    protected function _new_user_write($appname)
    {
        $email     = $this->input->post('email', true);
        $name      = strtolower($this->input->post('name', true));
        $lastname  = $this->input->post('lastname', true);
        $groupname = $this->input->post('group', true);
        $username  = $this->input->post('username', true);
        $password  = $this->input->post('password', true);
        
        $this->Users->createUser($appname, $email, $name, $lastname, $groupname, $username, $password);

        $this->session->set('new_user_saved', true);

        $this->new_user_read($appname);
        
    }
    // }}}
    // {{{ _user_profile
    protected function _user_profile($username, $flagform = true)
    {
        $flagsaved = false;

        if ($this->session->get('edit_user_saved')) {
            $flagsaved = true;
            $this->session->del('edit_user_saved');
        }

        $userprofile = $this->Users->getUserProfile($username);
        $group       = $this->Groups->getGroup($userprofile['group_id']);
        $row         = $this->Groups->getAllAppGroups($group['appname']);

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
    protected function _user_profile_write($appname)
    {
        $email     = $this->input->post('email', true);
        $name      = $this->input->post('name', true);
        $lastname  = $this->input->post('lastname', true);
        $groupname = $this->input->post('group', true);
        $username  = $this->input->post('username', true);
        $password  = $this->input->post('password', true);
        $blocked   = $this->input->post('blocked', true);

        $this->Users->editUser($appname, $email, $name, $lastname, $groupname, $username, $password, $blocked);

        $this->session->set('edit_user_saved', true);

        $this->user_profile_read($username);   
    }
    // }}}
    // {{{ user_profile_write_error
    public function user_profile_write_error()
    {
        $username = $this->input->post('username');
        $this->user_profile_read($username);
    }
    // }}}
    // {{{ _privileges_edit_read
    protected function _privileges_edit_read($id, $appname, $flagform = true)
    {

        $flagsaved = false;
        if ($this->session->get('group_privileges_edit_saved')) {
            $flagsaved = $this->session->get('group_privileges_edit_saved');
            $this->session->del('group_privileges_edit_saved');
        }

        $parents         = $this->Privileges->getFilteredPrivileges($appname, "0");
        $subpris         = $this->Privileges->getAppPrivileges($appname);
        $allselected     = $this->Privileges->getPrivileges($id, true);
        $selected        = $this->Privileges->getSelectedPrivileges($subpris, $allselected);
        $selectedparents = $this->Privileges->getSelectedParents($selected, $parents);

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
    protected function _privileges_edit_write($appname)
    {
        $ids     = $this->input->post('privileges');
        $groupid = $this->input->post('id');
        
        $this->Privileges->editPrivileges($ids, $groupid, $appname);

        $this->session->set('group_privileges_edit_saved', true);

        $this->group_privileges_edit_read($groupid, $appname);
    }
    // }}}
    // {{{ _check_app
    protected function _check_app($name)
    {
        return $this->Applications->hasApp($name);
    }
    // }}}
}
?>
