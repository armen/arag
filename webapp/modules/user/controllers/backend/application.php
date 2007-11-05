<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Application extends Backend 
{

    // {{{ Constructor
    function Application()
    {
        parent::Backend();

        // Global tabbedbock
        $this->global_tabs->setTitle(_("User Management"));
        $this->global_tabs->addItem(_("Groups"), 'user/backend/application/index');
        $this->global_tabs->additem(_("Users"), 'user/backend/application/all_users');
        $this->global_tabs->addItem(_("Default Group"), 'user/backend/application/default_group');
        $this->global_tabs->additem(_("New Group"), 'user/backend/application/new_group');
        $this->global_tabs->additem(_("New User"), 'user/backend/application/new_user');       
        $this->global_tabs->addItem(_("Settings"), 'user/backend/application/settings');
    }
    // }}}
    // {{{ index
    function index()
    {
        $this->_create_groups_list($this->appname);

        $this->groups->addAction('user/backend/application/group_privileges_edit/#id#/', _("Privileges"), 'privileges_action');
        $this->groups->addAction('user/backend/application/users/#id#/', _("View"), 'view_action'); 
        $this->groups->addAction('user/backend/application/delete/group/#id#', _("Delete"), 'delete_action');
        $this->groups->addAction("user/backend/application/delete/group", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->groups->setGroupActionParameterName('id');

        $this->session->set_userdata('delete_appname', $this->appname);

        $this->load->view('backend/groups');
    }
    // }}}
    // {{{ default_group_read
    function default_group_read($appname = NULL)
    {
        $appname = $this->appname;
        $this->_default_group($appname, false);
    }
    // }}}
    // {{{ new_group_read
    function new_group_read($appname = NULL)
    {
        $appname = $this->appname;
        $this->_new_group($appname, false);
    }
    // }}}
    // {{{ users_read
    function users_read($id, $appname = NULL)
    {
        
        $appname = $this->appname;
        $this->global_tabs->addItem(_("Users"), "user/backend/application/users/".$id);
        
        $this->_create_users_plist($id);

        $this->users->addAction("user/backend/application/user_profile/#username#", _("Edit"), 'edit_action');
        $this->users->addAction("user/backend/application/delete/user/#username#", _("Delete"), 'delete_action');
        $this->users->addAction("user/backend/application/delete/user", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username');
        
        $this->load->vars(array("flagsearch"  => false));        

        $this->load->view('backend/users');
    }
    // }}}
    // {{{ users_read_error
    function users_read_error()
    {
        $this->_invalid_request("user/backend/application/index");
    }
    // }}}
    // {{{ new_user_read
    function new_user_read()
    {
        $appname = $this->appname;
        $this->_new_user($appname, false);      
    }
    // }}}
    // {{{ user_profile_read
    function user_profile_read($username)
    {
        $this->global_tabs->addItem(_("User's Profile"), "user/backend/application/user_profile/%username%"); 
        $this->global_tabs->setParameter('username', $username);
        $this->_user_profile($username, false);    
    }
    // }}}
    // {{{ user_profile_read_error
    function user_profile_read_error()
    {
        $this->_invalid_request("user/backend/application/index");
    }
    // }}}
    // {{{ delete
    function delete($type, $objects = NULL)
    {
        if ($objects != NULL) {
            $this->global_tabs->addItem(_("Delete"), "user/backend/application/delete/".$type."/".$objects);
            $flag = false;
            if (is_numeric($objects)) {
                $flag = true;
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
            $this->global_tabs->addItem(_("Delete"), "user/backend/application/delete/".$type);
        }
        
        $subjects = array();

        foreach ($objects as $key) {

            $exist = false;

            if (preg_match("/^user$/", $type)) {
                $exist = $this->UsersModel->hasUserName($key);
                
                if (!$exist) {
                    $this->_invalid_request("user/backend/application/index");
                }
                
                array_push($subjects, $key);
            } else if (preg_match("/^group$/", $type)) {
                $exist = $this->GroupsModel->hasGroup(NULL, NULL, $key);
                
                if (!$exist) {
                    $this->_invalid_request("user/backend/application/index");
                }
                
                $row = $this->GroupsModel->getGroup($key);
                array_push($subjects, $row['name']);
            } else {
                $this->_invalid_request("user/backend/application/index");                
            }

        }
        
        $appname = $this->session->userdata('delete_appname');
        $this->session->unset_userdata('delete_appname');

        $subjects = implode(",", $subjects);
        
        $this->load->vars(array('objects'  => $objects,
                                'subjects' => $subjects,
                                'flag'     => $flag,
                                'appname'  => $appname,
                                'flagform' => false));

        $this->load->view('backend/delete');
    }
    // }}}
    // {{{ do_delete
    function do_delete()
    {
        $flag    = $this->input->post('flag');
        $objects = $this->input->post('objects');
         
        if ($this->input->post('submit')) {
            if ($flag) {
                $application = $this->input->post('application');
                $this->GroupsModel->deleteGroups($objects);
                redirect('user/backend/application/index/');
            } else {
                $this->UsersModel->deleteUsers($objects);
                redirect('user/backend/application/all_users');
            }
        } else {
            $this->_invalid_request("user/backend/application/index");
        }
    }
    //}}}
    //{{{ group_privileges_edit_read
    function group_privileges_edit_read($id, $appname = NULL)
    {
        $appname = $this->appname;
        $row  = $this->GroupsModel->getGroup($id);
        $name = $row['name'];
        $this->global_tabs->addItem(_("Edit $name's privileges"), "user/backend/application/group_privileges_edit/".$id);
        $this->_privileges_edit_read($id, $appname, false);
    }
    //}}}
    //{{{ group_privileges_edit_write
    function group_privileges_edit_write()
    {
        $this->_privileges_edit_write($this->appname);
    }
    //}}}
    // {{{ all_users
    function all_users($page = NULL)
    {
        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {        
            $user       = $this->session->userdata('user_user_user');
            $group_name = $this->session->userdata('user_user_group');
        } else {
            $user       = $this->input->post('user', True);
            $group_name = $this->input->post('group_name', True);
        }

        $this->session->set_userdata('user_user_user', $user);
        $this->session->set_userdata('user_user_group', $group_name);
        
        $app_name = $this->appname;
        
        $this->_create_users_plist(NULL, $app_name, $group_name, $user, false);

        $this->users->addAction("user/backend/application/user_profile/#username#", _("Edit"), 'edit_action');
        $this->users->addAction("user/backend/application/delete/user/#username#", _("Delete"), 'delete_action');
        $this->users->addAction("user/backend/application/delete/user", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username');
        
        $this->load->vars(array("flagsearch" => true,
                                "user"       => $user,
                                "app_name"   => $app_name,
                                "group_name" => $group_name,
                                "flagform"   => false));        

        $this->load->view('backend/users');    
    }
    // }}}
    // {{{ new_user_write()
    function new_user_write()
    {
        $this->_new_user_write($this->appname);
    }
    // }}}
    // {{{ new_user_write_error
    function new_user_write_error()
    {
        $this->new_user_read($this->appname);
    }
    // }}}
    // {{{ user_profile_write
    function user_profile_write()
    {
        $this->_user_profile_write($this->appname);       
    }
    // }}}
    // {{{ default_group_write
    function default_group_write()
    {
         $this->_default_group_write($this->appname);
    }
    // }}} 
    // {{{ new_group_write
    function new_group_write()
    {
         $this->_new_group_write($this->appname);              
    }
    // }}}
    // {{{ new_group_write_error
    function new_group_write_error()
    {
        $this->new_group_read($this->appname);
    }
    // }}}
}
?>
