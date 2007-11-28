<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller 
{
    // {{{ Properties
    
    protected $appname = Null;

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

        // Default page title
        $this->layout->page_title = 'User Management';

        // Get the appname
        $this->appname = $this->session->get('appname');
    }
    // }}}
    // {{{ index
    public function index()
    {
        if (defined('MASTERAPP')) {
            url::redirect("user/backend/applications");
        } else {
            url::redirect("user/backend/application");
        }
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
    // {{{ _settings_read
    public function _settings_read($flagform)
    {   
        $data          = Array();
        $data['limit'] = Arag_Config::get("limit");
        $data['saved'] = $this->session->get_once('settings_saved');
        $data['form']  = $flagform;

        $this->layout->content = new View('backend/settings', $data);
    }
    // }}}
    // {{{ _create_privileges_list
    protected function _create_privileges_list($appname, $parentid = NULL)
    {
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

        $data = array("parentid"  => $parentid,
                      "flagsaved" => $this->session->get_once('privileges_add_saved'));

        $this->layout->content = new View('backend/privileges', $data); 
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
        $row  = $this->Groups->getAllAppGroups($appname);
        $row2 = $this->Groups->getDefaultGroup($appname);

        $this->global_tabs->setParameter('name', $appname);

        $data = array("allgroups"    => $row,
                      "defaultgroup" => $row2[0]['default_group'],
                      "flagsaved"    => $this->session->get_once('default_group_saved'),
                      "flagform"     => $flagform,
                      "appname"      => $appname);

        $this->layout->content = new View('backend/default_group', $data);
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
        $this->global_tabs->setParameter('name', $appname); 

        $data = array("flagsaved" => $this->session->get_once('new_group_saved'),
                      "flagform"  => $flagform,
                      "appname"   => $appname);

        $this->layout->content = new View('backend/new_group', $data);
    }
    // }}}
    // {{{ _new_group_write
    protected function _new_group_write($appname)
    {
        $newgroup   = $this->input->post("newgroup", true);
            
        $this->Groups->newGroup($appname, $newgroup, $this->session->get('username'));

        $this->session->set('new_group_saved', true);

        $this->new_group_read($appname, true);
    }
    // }}}
    // {{{ _new_user
    protected function _new_user($appname, $flagform = true)
    {
        $this->global_tabs->setParameter('name', $appname); 
        
        $row  = $this->Groups->getAllAppGroups($appname);
        $row2 = $this->Groups->getDefaultGroup($appname);

        $data = array("appname"      => $appname,
                      "flagsaved"    => $this->session->get_once('new_user_saved'),
                      "allgroups"    => $row,
                      "defaultgroup" => $row2[0]['default_group'],
                      "flagform"     => $flagform);

        $this->layout->content = new View('backend/new_user', $data);       
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
        
        $this->Users->createUser($appname, $email, $name, $lastname, $groupname, $username, $password, $this->session->get('username'));

        $this->session->set('new_user_saved', true);

        $this->new_user_read($appname);
        
    }
    // }}}
    // {{{ _user_profile
    protected function _user_profile($username, $flagform = true, $oldpassword = false)
    {
        $userprofile = $this->Users->getUserProfile($username);
        $group       = $this->Groups->getGroup($userprofile['group_id']);
        $row         = $this->Groups->getAllAppGroups($group['appname']);

        $data = array("appname"      => $group['appname'],
                      "flagsaved"    => $this->session->get_once('edit_user_saved'),
                      "allgroups"    => $row,
                      "defaultgroup" => $group['name'],
                      "username"     => $userprofile['username'],
                      "name"         => $userprofile['name'],
                      "lastname"     => $userprofile['lastname'],
                      "email"        => $userprofile['email'],
                      "blocked"      => $userprofile['blocked'],
                      "flagform"     => $flagform,
                      "oldpassword"  => $oldpassword);

        $this->layout->content = new View('backend/user_profile', $data);
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

        $this->Users->editUser($appname, $email, $name, $lastname, $groupname, $username, $password, $blocked, $this->session->get('username'));

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
        $parents         = $this->Privileges->getFilteredPrivileges($appname, 0);
        $subpris         = $this->Privileges->getAppPrivileges($appname);
        $allselected     = $this->Privileges->getPrivileges($id, true);
        $selected        = $this->Privileges->getSelectedPrivileges($subpris, $allselected);
        $selectedparents = $this->Privileges->getSelectedParents($selected, $parents);

        $data = array('parent_privileges' => $selectedparents,
                      'sub_privileges'    => $selected,
                      'id'                => $id,
                      'appname'           => $appname,
                      'flagsaved'         => $this->session->get_once('group_privileges_edit_saved'),
                      'flagform'          => $flagform);

        $this->layout->content = new View('backend/group_privileges', $data);
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
    // {{{ callbacks
    // {{{ _check_app
    public function _check_app($name)
    {
        return $this->Applications->hasApp($name);
    }
    // }}}
    // {{{ _check_group
    public function _check_group($id)
    {
        return $this->Groups->hasGroup(NULL, NULL, $id);
    }
    // }}}
    // {{{ _check_group_name
    public function _check_group_name($name)
    {
        $appname = $this->input->post('application');
        return !$this->Groups->hasGroup($name, $appname);
    }
    // }}}
    // {{{ _check_user_name
    public function _check_user_name($username)
    {
        return (!preg_match("/^[a-z0-9_.]+_admin$/", strtolower($username)) && 
                !$this->Users->hasUserName($username) && preg_match("/^[a-z][a-z0-9_.]*$/", strtolower($username)));
    }
    // }}}
    // {{{ _check_user_name_profile
    public function _check_user_name_profile($username)
    {   
        return $this->Users->hasUserName($username, $this->appname);
    }
    // }}}
    // {{{ _check_user_name_profile_master
    public function _check_user_name_profile_master($username)
    {   
        return $this->Users->hasUserName($username);
    }
    // }}}   
    // {{{ _check_filter
    public function _check_filter($filter)
    {
        return ($filter === '*') || preg_match('/^([a-z_]+)((\/[a-z_]+){0,2}(\/\*))$/', $filter);
    }
    // }}}
    // {{{ _check_privilege
    public function _check_privilege($privilege)
    {
        if ($this->input->post('parentid') === "0") {
            return true;
        }
        return (boolean) preg_match('/^([a-z_]+)((\/[a-z_]+){0,2}(\/\*))|((\/[a-z_]+){2,3})$/', strtolower(trim($privilege, '/')));
    }
    // }}}
    // {{{ _check_label
    public function _check_label($id)
    {
        return $this->Privileges->hasLabel($id);
    }
    // }}}
    // {{{ _check_app_filter
    public function _check_app_filter($appname)
    {
        return !$this->Filters->hasApp($appname);
    }
    // }}}
    // {{{ _ckeck_current_group
    public function _check_current_group($id)
    {
        return $this->Groups->hasGroup(NULL, $this->appname, $id);
    }
    // }}}
    // {{{ _check_current_deletables
    public function _check_current_deletables()
    {
        $objects = $this->validation->objects;

        foreach ($objects as $object) {
            if ($this->validation->flag == 1) {
                if (!$this->Groups->hasGroup(NULL, $this->appname, $object)) {
                    return false;
                }
            } else {
                if (!$this->Users->hasUserName($object, $this->appname)) {
                    return false;
                }
            }
        }

        return true;
    }
    // }}}
    // {{{ _check_old_password
    public function _check_old_password($password = NULL, $username = NULL)
    {
        if ($password == NULL) {
            $password = $this->validation->oldpassword;
        }       

        if ($username == NULL) {
            $username = $this->validation->username;
        }

        $status = $this->Users->check($username, $password);

        if ($status != Users_Model::USER_OK) {
            return false;
        }

        return true;
    }
    // }}}
    // {{{ _check_password
    public function _check_password($password)
    {
        if ($password != "") {
            return $this->_check_old_password();
        }

        return true;
    }
    // }}}
    // }}}
}
?>
