<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Application_Controller extends Backend_Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Global tabbedbock
        $this->global_tabs->setTitle(_("User Management"));
        $this->global_tabs->addItem(_("Groups"), 'user/backend/application/index');
        $this->global_tabs->additem(_("Users"), 'user/backend/application/all_users');
        $this->global_tabs->addItem(_("Default Group"), 'user/backend/application/default_group');
        $this->global_tabs->additem(_("New Group"), 'user/backend/application/new_group');
        $this->global_tabs->additem(_("New User"), 'user/backend/application/new_user');
        $this->global_tabs->addItem(_("Settings"), 'user/backend/application/settings');

        // Validation Messages
        $passwordLength = Arag_Config::get("passlength");
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('_check_group_name', _("This %s is not available"));
        $this->validation->message('_check_user_name', _("This %s is reserved or not available"));
        $this->validation->message('matches', _("%ss do not match"));
        $this->validation->message('alpha_dash', _("%s can contain only alpha-numeric characters, underscores or dashes"));
        $this->validation->message('alpha', _("%s can contain only alpha characters"));
        $this->validation->message('type', _("%s should be numeric"));
        $this->validation->message('email', _("Please enter a valid email address"));
        $this->validation->message('length', _("%s should be at least 4 characters"));
        $this->validation->message('_check_password', _("Please enter correct %s"));
        $this->validation->message('numeric', _("%s should be numeric."));
        $this->validation->message('password_length',sprintf(_("Password length should be at least %s characters "), $passwordLength));
    }
    // }}}
    // {{{ index
    public function index()
    {
        $this->_create_groups_list($this->appname);

        $this->groups->addAction('user/backend/application/group_privileges_edit/#id#/', _("Privileges"), 'privileges_action');
        $this->groups->addAction('user/backend/application/users/#id#/', _("Users List"), 'users_list');
        $this->groups->addAction('user/backend/application/delete/group/#id#', _("Delete"), 'delete_action');
        $this->groups->addAction("user/backend/application/delete/group", _("Delete"), 'delete_action', False, PList_Component::GROUP_ACTION);
        $this->groups->setGroupActionParameterName('id');

        $this->session->set('delete_appname', $this->appname);

        $this->layout->content = new View('backend/groups');
    }
    // }}}
    // {{{ default_group_read
    public function default_group_read()
    {
        $this->_default_group($this->appname, false);
    }
    // }}}
    // {{{ new_group_read
    public function new_group_read()
    {
        $this->_new_group($this->appname, false);
    }
    // }}}
    // {{{ users_read
    public function users_read($id)
    {
        $this->global_tabs->addItem(_("Users"), "user/backend/application/users/".$id);

        $this->_create_users_plist($id);

        $this->users->addAction("user/backend/application/user_profile/#username#", _("Edit"), 'edit_action');
        $this->users->addAction("user/backend/application/delete/user/#username#", _("Delete"), 'delete_action');
        $this->users->addAction("user/backend/application/delete/user", _("Delete"), 'delete_action', False, PList_Component::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username');

        $this->layout->content = new View('backend/users', array("flagsearch" => false));
    }
    // }}}
    // {{{ users_read_error
    public function users_read_error()
    {
        $this->_invalid_request("user/backend/application/index", _("Invalid ID"));
    }
    // }}}
    // {{{ new_user_read
    public function new_user_read()
    {
        $this->_new_user($this->appname, false);
    }
    // }}}
    // {{{ user_profile_read
    public function user_profile_read($username)
    {
        $this->global_tabs->addItem(_("User's Profile"), "user/backend/application/user_profile/%username%");
        $this->global_tabs->setParameter('username', $username);
        $this->_user_profile($username, false, true);
    }
    // }}}
    // {{{ user_profile_validate_read
    public function user_profile_validate_read()
    {
        $this->validation->name(0, _("Username"))->add_rules(0, 'required', array($this, '_check_user_name_profile'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ user_profile_read_error
    public function user_profile_read_error()
    {
        $this->_invalid_request("user/backend/application/index", _("Invalid Username"));
    }
    // }}}
    // {{{ delete
    public function delete($type, $objects = NULL)
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
            $this->global_tabs->addItem(_("Delete"), 'user/backend/application/delete/'.$type);
        }

        $subjects = array();

        foreach ($objects as $key) {

            $exist = false;

            if (preg_match('/^user$/', $type)) {
                $exist = $this->Users->hasUserName($key);

                if (!$exist) {
                    $this->_invalid_request('user/backend/application/index', _("Invalid Type"));
                }

                array_push($subjects, $key);
            } else if (preg_match("/^group$/", $type)) {
                $exist = $this->Groups->hasGroup(NULL, NULL, $key);

                if (!$exist) {
                    $this->_invalid_request('user/backend/application/index', _("Invalid Type"));
                }

                $row = $this->Groups->getGroup($key);
                array_push($subjects, $row['name']);
            } else {
                $this->_invalid_request("user/backend/application/index", _("Invalid Object"));
            }

        }

        $appname = $this->session->get_once('delete_appname');

        $subjects = implode(",", $subjects);

        $data = array('objects'  => $objects,
                      'subjects' => $subjects,
                      'flag'     => $flag,
                      'appname'  => $appname,
                      'flagform' => false);

        $this->layout->content = new View('backend/delete', $data);
    }
    // }}}
    // {{{ do_delete
    public function do_delete()
    {
        $flag    = $this->input->post('flag');
        $objects = $this->input->post('objects');

        if ($this->input->post('submit')) {
            if ($flag) {
                $application = $this->input->post('application');
                $this->Groups->deleteGroups($objects, $this->session->get('user.username'));
                url::redirect('user/backend/application/index/');
            } else {
                $this->Users->deleteUsers($objects, NULL, $this->session->get('user.username'));
                url::redirect('user/backend/application/all_users');
            }
        } else {
            $this->_invalid_request("user/backend/application/index", _("No form is submitted"));
        }
    }
    //}}}
    // {{{ do_delete_validate
    public function do_delete_validate()
    {
        $this->validation->name('objects', _("Objects"))->add_rules(array($this, '_check_current_deletables'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ do_delete_error
    public function do_delete_error()
    {
        $this->_invalid_request('user/backend/application', _("Invalid Object"));
    }
    // }}}
    //{{{ group_privileges_edit_read
    public function group_privileges_edit_read($id, $appname = NULL)
    {
        $row  = $this->Groups->getGroup($id);
        $name = $row['name'];
        $this->global_tabs->addItem(_("Edit $name's privileges"), "user/backend/application/group_privileges_edit/".$id);
        $this->_privileges_edit_read($id, $this->appname, false);
    }
    //}}}
    //{{{ group_privileges_edit_write
    public function group_privileges_edit_write()
    {
        $this->_privileges_edit_write($this->appname);
    }
    //}}}
    // {{{ all_users
    public function all_users($page = NULL)
    {
        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {
            $user       = $this->session->get('user_user_user');
            $group_name = $this->session->get('user_user_group');
        } else {
            $user       = $this->input->post('user', Null, True);
            $group_name = $this->input->post('group_name', Null, True);
        }

        $this->session->set('user_user_user', $user);
        $this->session->set('user_user_group', $group_name);

        $this->_create_users_plist(NULL, $this->appname, $group_name, $user, false);

        $this->users->addAction("user/backend/application/user_profile/#username#", _("Edit"), 'edit_action');
        $this->users->addAction("user/backend/application/delete/user/#username#", _("Delete"), 'delete_action');
        $this->users->addAction("user/backend/application/delete/user", _("Delete"), 'delete_action', False, PList_Component::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username');

        $data = array("flagsearch" => true,
                      "user"       => $user,
                      "app_name"   => $this->appname,
                      "group_name" => $group_name,
                      "flagform"   => false);

        $this->layout->content = new View('backend/users', $data);
    }
    // }}}
    // {{{ new_user_write
    public function new_user_write()
    {
        $this->_new_user_write($this->appname);
    }
    // }}}
    // {{{ new_user_validate_write
    public function new_user_validate_write()
    {
        $this->validation->name('username', _("Username"))->pre_filter('trim', 'username')
             ->add_rules('username', 'required', 'valid::alpha_dash', array($this, '_check_user_name'), 'length[4, 255]');

        $passwordLength = Arag_Config::get("passlength");
        $this->validation->name('password', _("Password"))->pre_filter('trim', 'password')
             ->add_rules('password', 'required', 'length['.$passwordLength.', 255]', 'matches[repassword]');

        $this->validation->name('repassword', _("Repassword"))->pre_filter('trim', 'repassword')
             ->add_rules('repassword', 'required');

        $this->validation->name('name', _("Name"))->pre_filter('trim', 'name')
             ->add_rules('name', 'required', 'valid::alpha');

        $this->validation->name('lastname', _("Lastname"))->pre_filter('trim', 'lastname')
             ->add_rules('lastname', 'required', 'valid::alpha');

        $this->validation->name('email', _("Email"))->pre_filter('trim', 'email')
             ->add_rules('email', 'required', 'valid::email');

        $this->validation->name('group', _("Group"))->pre_filter('trim', 'group')
             ->add_rules('group', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ new_user_write_error
    public function new_user_write_error()
    {
        $this->new_user_read($this->appname);
    }
    // }}}
    // {{{ user_profile_write
    public function user_profile_write()
    {
        if ($this->input->post('password') && !$this->_check_old_password($this->input->post('oldpassword'), $this->input->post('username'))) {
            $this->_invalid_request('user/backend/application/index', _("Invalid Password change request"));
        }
        $this->_user_profile_write($this->appname);
    }
    // }}}
    // {{{ user_profile_validate_write
    public function user_profile_validate_write()
    {
        $this->validation->name('username', _("Username"))->pre_filter('trim', 'username')
             ->add_rules('username', array($this, '_check_user_name_profile'));

        $passwordLength = Arag_Config::get("passlength");
        $this->validation->name('oldpassword', _("Old Password"))->pre_filter('trim', 'oldpassword')
             ->add_rules('oldpassword', 'length['.$passwordLength.', 255]', array($this, '_check_password'));

        $this->validation->name('password', _("Password"))->pre_filter('trim', 'password')
             ->add_rules('repassword', 'matches[repassword]', 'length['.$passwordLength.', 255]');

        $this->validation->name('name', _("Name"))->pre_filter('trim', 'name')
             ->add_rules('name', 'required', 'valid::alpha');

        $this->validation->name('lastname', _("Lastname"))->pre_filter('trim', 'lastname')
             ->add_rules('lastname', 'required', 'valid::alpha');

        $this->validation->name('email', _("Email"))->pre_filter('trim', 'email')
             ->add_rules('email', 'required', 'valid::email');

        $this->validation->name('group', _("Group"))->pre_filter('trim', 'group')
             ->add_rules('group', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ default_group_write
    public function default_group_write()
    {
         $this->_default_group_write($this->appname);
    }
    // }}}
    // {{{ new_group_write
    public function new_group_write()
    {
         $this->_new_group_write($this->appname);
    }
    // }}}
    // {{{ new_group_validate_write
    public function new_group_validate_write()
    {
        $this->validation->name('newgroup', _("Name for new group"))->pre_filter('trim', 'newgroup')
             ->add_rules('newgroup', 'required', array($this, '_check_group_name'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ new_group_write_error
    public function new_group_write_error()
    {
        $this->new_group_read($this->appname);
    }
    // }}}
    // {{{ settings_read
    public function settings_read()
    {
        $this->_settings_read(false);
    }
    // }}}
    // {{{ settings_write
    public function settings_write()
    {
        Arag_Config::set('limit', $this->input->post('limit'));


        $this->session->set('settings_saved', true);

        //url::redirect('user/backend/applications/settings');
        $this->settings_read();
    }
    // }}}
    // {{{ settings_validate_write
    public function settings_validate_write()
    {
        $this->validation->name('limit', _("Limit"))->pre_filter('trim', 'limit')
             ->add_rules('limit', 'valid::numeric');

        $this->validation->validate();
    }
    // }}}
    // {{{ settings_write_error
    public function settings_write_error()
    {
        $this->settings_read();
    }
    // }}}
}
