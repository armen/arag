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
    // {{{ Constructor
    function Applications()
    {   
        parent::Backend();

        // Global tabbedbock
        $this->global_tabs->setTitle(_("User Management"));
        $this->global_tabs->addItem(_("Apllications"), 'user/backend/applications');
        $this->global_tabs->addItem(_("Groups"), 'user/backend/applications/groups/%name%', 'user/backend/applications');
        $this->global_tabs->addItem(_("Default Group"), 'user/backend/applications/default_group/%name%', 'user/backend/applications');
        $this->global_tabs->additem(_("New Group"), 'user/backend/applications/new_group/%name%', 'user/backend/applications');
        $this->global_tabs->additem(_("New User"), 'user/backend/applications/new_user/%name%', 'user/backend/applications');       
        $this->global_tabs->addItem(_("Users"), 'user/backend/applications/all_users');
        $this->global_tabs->addItem(_("Settings"), 'user/backend/applications/settings');

    }
    // }}}
    // {{{ index
    function index($page = Null)
    {
        $this->load->component('PList', 'applications');

        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {        
            $name = $this->session->userdata('user_app_name');
        } else {
            $name = $this->input->post('name', True);
        }

        $this->session->set_userdata('user_app_name', $name);

        $this->applications->setResource($this->ApplicationsModel->getApps($name));
        $this->applications->setLimit($this->config->item('limit', NULL, 0));
        $this->applications->addColumn('name', _("Name"));        
        $this->applications->addColumn('default_group', _("Default Group"));
        $this->applications->addColumn('ApplicationsModel.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->applications->addAction('user/backend/applications/groups/#name#', _("Edit"), 'edit_action');      
        
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
    // {{{ groups_read
    function groups_read($appname)
    {
        $this->load->component('PList', 'groups');

        $this->global_tabs->setParameter('name', $appname);

        $this->groups->setResource($this->GroupsModel->getGroups($appname));
        $this->groups->setLimit($this->config->item('limit', NULL, 0));
        $this->groups->addColumn('id', Null, PList::HIDDEN_COLUMN);       
        $this->groups->addColumn('name', _("Name"));
        $this->groups->addColumn('appname', _("Application"));
        $this->groups->addColumn('modified_by', _("Modified By"));
        $this->groups->addColumn('ApplicationsModel.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->groups->addColumn('ApplicationsModel.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);
        $this->groups->addAction('user/backend/applications/users/#id#/'.$appname, _("Edit"), 'edit_action'); 
        $this->groups->addAction('user/backend/applications/delete/group/#id#', _("Delete"), 'delete_action');
        $this->groups->addAction("user/backend/applications/delete/group", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->groups->setGroupActionParameterName('id');

        $this->session->set_userdata('delete_appname', $appname);

        $this->load->view('backend/groups'); 
    }
    // }}}
    // {{{ groups_read_error
    function groups_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");        
    }
    // }}}
    // {{{ default_group_read
    function default_group_read($appname, $flagsaved = false)
    {
        $row  = $this->GroupsModel->allAppGroups($appname);
        $row2 = $this->GroupsModel->defaultGroup($appname);

        $this->global_tabs->setParameter('name', $appname);

        $this->load->vars(array("allgroups"    => $row,
                                "defaultgroup" => $row2[0]['default_group'],
                                "appname"      => $appname,
                                "flagsaved"    => $flagsaved));

        $this->load->view('backend/default_group');
    }
    // }}}
    // {{{ default_group_read_error
    function default_group_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");
    }
    // }}}
    // {{{ default_group_write
    function default_group_write()
    {
        $appname = $this->input->post("application");
        $group   = $this->input->post("dgroup");
            
        $this->GroupsModel->setGroup($appname, $group);

        $this->default_group_read($appname, true);
    }
    // }}}
    // {{{ new_group_read
    function new_group_read($appname, $flagsaved = false)
    {
        $this->global_tabs->setParameter('name', $appname); 

        $this->load->vars(array("appname"      => $appname,
                                "flagsaved"    => $flagsaved));

        $this->load->view('backend/new_group.tpl');
    }
    // }}}
    // {{{ new_group_read_error
    function new_group_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");
    }
    // }}}
    // {{{ new_group_write
    function new_group_write()
    {
        $appname    = $this->input->post("application", true);
        $newgroup   = $this->input->post("newgroup", true);
        $modifier   = $this->session->userdata('username');
            
        $this->GroupsModel->newGroup($appname, $newgroup, $modifier);

        $this->new_group_read($appname, true);
    }
    // }}}
    // {{{ new_group_write_error
    function new_group_write_error()
    {
        $appname = $this->input->post("application", true);
        $this->new_group_read($appname);
    }
    // }}}
    // {{{ users_read
    function users_read($id, $appname)
    {
        $this->global_tabs->setParameter('name', $appname);
 
        $this->global_tabs->addItem(_("Users"), "user/backend/applications/users/".$id."/".$appname, "user/backend/applications");
        
        $this->_create_users_plist($id);
        
        $this->load->vars(array("flagsearch"  => false));        

        $this->load->view('backend/users');
    }
    // }}}
    // {{{ users_read_error
    function users_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");
    }
    // }}}
    // {{{ all_users
    function all_users($page = NULL)
    {
        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {        
            $user       = $this->session->userdata('user_user_user');
            $app_name   = $this->session->userdata('user_user_app');
            $group_name = $this->session->userdata('user_user_group');
        } else {
            $user       = $this->input->post('user', True);
            $app_name   = $this->input->post('app_name', True);
            $group_name = $this->input->post('group_name', True);
        }

        $this->session->set_userdata('user_user_user', $user);
        $this->session->set_userdata('user_user_app', $app_name);
        $this->session->set_userdata('user_user_group', $group_name);
        
        $this->_create_users_plist(NULL, $app_name, $group_name, $user);
        
        $this->load->vars(array("flagsearch" => true,
                                "user"       => $user,
                                "app_name"   => $app_name,
                                "group_name" => $group_name));        

        $this->load->view('backend/users');    
    }
    // }}}
    // {{{ new_user_read
    function new_user_read($appname, $flagsaved = false)
    {
        $this->global_tabs->setParameter('name', $appname); 
        
        $row  = $this->GroupsModel->allAppGroups($appname);
        $row2 = $this->GroupsModel->defaultGroup($appname);

        $this->load->vars(array("appname"      => $appname,
                                "flagsaved"    => $flagsaved,
                                "allgroups"    => $row,
                                "defaultgroup" => $row2[0]['default_group']));

        $this->load->view('backend/new_user.tpl');       
    }
    // }}}
    // {{{ new_user_read_error
    function new_user_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");
    }
    // }}}
    // {{{ new_user_write
    function new_user_write()
    {
        $appname   = $this->input->post('application', true);    
        $email     = $this->input->post('email', true);
        $name      = strtolower($this->input->post('name', true));
        $lastname  = $this->input->post('lastname', true);
        $groupname = $this->input->post('group', true);
        $username  = $this->input->post('username', true);
        $password  = $this->input->post('password', true);
        
        $this->UsersModel->createUser($appname, $email, $name, $lastname, $groupname, $username, $password);

        $this->new_user_read($appname, true);
        
    }
    // }}}
    // {{{ new_user_write_error
    function new_user_write_error()
    {
        $appname = $this->input->post('application');
        $this->new_user_read($appname);
    }
    // }}}
    // {{{ user_profile_read
    function user_profile_read($username, $flagsaved = false)
    {
        $this->global_tabs->addItem(_("User's Profile"), "user/backend/applications/user_profile/".$username);     

        $userprofile = $this->UsersModel->getUserProfile($username);
        $group       = $this->GroupsModel->getGroup($userprofile['group_id']);
        $row         = $this->GroupsModel->allAppGroups($group['appname']);

        $this->load->vars(array("appname"      => $group['appname'],
                                "flagsaved"    => $flagsaved,
                                "allgroups"    => $row,
                                "defaultgroup" => $group['name'],
                                "username"     => $userprofile['username'],
                                "name"         => $userprofile['name'],
                                "lastname"     => $userprofile['lastname'],
                                "email"        => $userprofile['email']));

        $this->load->view('backend/user_profile.tpl');       
    }
    // }}}
    // {{{ user_profile_read_error
    function user_profile_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");
    }
    // }}}
    // {{{ user_profile_write
    function user_profile_write()
    {
        $appname   = $this->input->post('application', true);    
        $email     = $this->input->post('email', true);
        $name      = $this->input->post('name', true);
        $lastname  = $this->input->post('lastname', true);
        $groupname = $this->input->post('group', true);
        $username  = $this->input->post('username', true);
        $password  = $this->input->post('password', true);
        
        $this->UsersModel->editUser($appname, $email, $name, $lastname, $groupname, $username, $password);

        $this->user_profile_read($username, true);   
    }
    // }}}
    // {{{ user_profile_write_error
    function user_profile_write_error()
    {
        $username = $this->input->post('username');
        $this->user_profile_read($username);
    }
    // }}}
    // {{{ delete
    function delete($type, $objects = NULL)
    {
        if ($objects != NULL) {
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/delete/".$type."/".$objects);
            $flag = true;
            if (is_numeric($objects)) {
                $flag = false;
            }
            $objects = array($objects);
        } else {
            if ($this->input->post('id')) {
                $objects = $this->input->post('id');
                $flag = true;
            } else {
                $objects = $this->input->post('username');
                $flag = false;
            }
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/delete/".$type);
        }
        
        $subjects = array();

        foreach ($objects as $key) {

            $exist = false;

            if (preg_match("/^user$/", $type)) {
                $exist = $this->UsersModel->hasUserName($key);
                
                if (!$exist) {
                    $this->_invalid_request("user/backend/applications/index");
                }
                
                array_push($subjects, $key);
            } else if (preg_match("/^group$/", $type)) {
                $exist = $this->GroupsModel->hasGroup(NULL, NULL, $key);
                
                if (!$exist) {
                    $this->_invalid_request("user/backend/applications/index");
                }
                
                $row = $this->GroupsModel->getGroup($key);
                array_push($subjects, $row['name']);
            }

        }
        
        $appname = $this->session->userdata('delete_appname');
        $this->session->unset_userdata('delete_appname');

        $subjects = implode(",", $subjects);
        
        $this->load->vars(array('objects'  => $objects,
                                'subjects' => $subjects,
                                'flag'     => $flag,
                                'appname'  => $appname));

        $this->load->view('backend/delete');
    }
    // }}}
    // {{{ settings_read
    function settings_read($saved = NULL)
    {
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

        redirect('user/backend/applications/settings/saved');
    }
    // }}}
    // {{{ settings_write_error
    function settings_write_error()
    {
        $this->settings_read();
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
        $this->users->addAction("user/backend/applications/user_profile/#username#", _("Edit"), 'edit_action');
        $this->users->addAction("user/backend/applications/delete/user/#username#", _("Delete"), 'delete_action');
        $this->users->addAction("user/backend/applications/delete/user", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username'); 

    }
    // }}}
    // {{{ _check_app
    function _check_app($name)
    {
        return $this->ApplicationsModel->hasApp($name);
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
        return (!preg_match("/^[a-z0-9_.]*+_admin$/", strtolower($username)) && !$this->UsersModel->hasUserName($username));
    }
    // }}}
    // {{{ _check_user_name_profile
    function _check_user_name_profile($username)
    {
        return !$this->UsersModel->hasUserName($username);
    }
    // }}}
}
?>
