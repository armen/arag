<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Applications_Controller extends User_Backend
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Global tabbedbock
        $this->global_tabs->setTitle(_("User Management"));
        $this->global_tabs->addItem(_("Apllications"), 'user/backend/applications/index');
        $this->global_tabs->addItem(_("Apllications"), 'user/backend/applications/index', 'user/backend/applications/index');
        $this->global_tabs->addItem(_("Users"), 'user/backend/applications/all_users');
        $this->global_tabs->addItem(_("Users List"), 'user/backend/applications/all_users', 'user/backend/applications/all_users');
        $this->global_tabs->addItem(_("Filters"), 'user/backend/applications/apps_filters');
        $this->global_tabs->addItem(_("Filters"), 'user/backend/applications/apps_filters', 'user/backend/applications/apps_filters');
        $this->global_tabs->addItem(_("Add Application"), 'user/backend/applications/add_apps_filters', 'user/backend/applications/apps_filters');
        $this->global_tabs->addItem(_("Privileges"), 'user/backend/applications/privileges_parents');
        $this->global_tabs->addItem(_("Parents"), 'user/backend/applications/privileges_parents', 'user/backend/applications/privileges_parents');
        $this->global_tabs->addItem(_("All"), 'user/backend/applications/privileges_all', 'user/backend/applications/privileges_parents');
        $this->global_tabs->addItem(_("Deploy"), 'user/backend/applications/privileges_deploy', 'user/backend/applications/privileges_parents');
        $this->global_tabs->addItem(_("Settings"), 'user/backend/applications/settings');
        $this->global_tabs->addItem(_("Page Limit"), 'user/backend/applications/settings', 'user/backend/applications/settings');
        $this->global_tabs->addItem(_("Password Settings"), 'user/backend/applications/password', 'user/backend/applications/settings');
        $this->global_tabs->addItem(_("Expire Time"), 'user/backend/applications/expire_time', 'user/backend/applications/settings');
        $this->global_tabs->addItem(_("User Blocking"), 'user/backend/applications/user_blocking', 'user/backend/applications/settings');
        $this->global_tabs->addItem(_("Email Template"), 'user/backend/applications/email_template', 'user/backend/applications/settings');
        $this->global_tabs->addItem(_("New Application"), 'user/backend/applications/add', 'user/backend/applications/index');

        // Validation Messages
        $passwordLength = Arag_Config::get("passlength");
        $this->validation->message('standard_text', _("%s should be standard text."));
        $this->validation->message('numeric', _("%s should be numeric."));
        $this->validation->message('required', _("%s is required"));
        $this->validation->message('_check_group_name', _("This %s is not available"));
        $this->validation->message('_check_group', _("This %s is not available"));
        $this->validation->message('_check_user_name', _("This %s is reserved or not available"));
        $this->validation->message('matches', _("%ss do not match"));
        $this->validation->message('alpha_dash', _("%s can contain only alpha-numeric characters, underscores or dashes"));
        $this->validation->message('alpha', _("%s can contain only alpha characters"));
        $this->validation->message('type', _("%s should be numeric"));
        $this->validation->message('email', _("Please enter a valid email address"));
        $this->validation->message('length', _("%s should be at least 4 characters"));
        $this->validation->message('_check_filter', _("Please enter a valid %s"));
        $this->validation->message('_check_privilege', _("Please enter a valid %s"));
        $this->validation->message('_check_app_filter', _("This application filter exists"));
        $this->validation->message('password_length', sprintf(_("Password length should be at least %s characters "), $passwordLength));
    }
    // }}}
    // {{{ index
    // {{{ index
    public function index_any($page = Null)
    {
        $applications = new PList_Component('applications');

        if ($page != Null && preg_match('|^page_applications$|', $page)) {
            $name = $this->session->get('user_app_name');
        } else {
            $name = $this->input->post('name', Null, True);
        }

        $this->session->set('user_app_name', $name);

        $applications->setResource($this->Applications->getApps($name, false));
        $applications->setLimit(Arag_Config::get('limit', 0));
        $applications->addColumn('name', _("Name"));
        $applications->addColumn('default_group', _("Default Group"));
        $applications->addColumn('Applications.getDate', _("Create Date"), PList_Component::VIRTUAL_COLUMN);
        $applications->addAction('user/backend/applications/groups/#name#', _("Edit"), 'edit_action');

        $this->layout->content = new View('backend/index', array("name" => $name, "flag" => false));
    }
    // }}}
    // {{{ index_validate_write
    public function index_validate_write()
    {
        $this->validation->name('name', _("Name"))->pre_filter('trim', 'name')
             ->add_rules('name', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ index_any_error
    public function index_any_error()
    {
        $this->index_any();
    }
    // }}}
    // }}}
    // {{{ groups
    // {{{ groups_read
    public function groups_read($appname)
    {
        $this->_create_groups_list($appname);

        $this->global_tabs->setParameter('appname', $appname);

        $this->global_tabs->addItem(_("Groups of '%appname%'"), 'user/backend/applications/groups/%appname%', 'user/backend/applications');
        $this->global_tabs->addItem(_("Default Group of '%appname%'"), 'user/backend/applications/default_group/%appname%', 'user/backend/applications');
        $this->global_tabs->additem(_("New Group for '%appname%'"), 'user/backend/applications/new_group/%appname%', 'user/backend/applications');
        $this->global_tabs->additem(_("New User for '%appname%'"), 'user/backend/applications/new_user/%appname%', 'user/backend/applications');

        $this->groups->addAction('user/backend/applications/group_privileges_edit/#id#/'.$appname, _("Privileges"), 'privileges_action');
        $this->groups->addAction('user/backend/applications/users/#id#/'.$appname, _("Users List"), 'users_list');
        $this->groups->addAction('Applications_Controller::_deleteGroup', PLIST_Component::VIRTUAL_COLUMN);
        $this->groups->addAction("user/backend/applications/delete/group", _("Delete"), 'delete_action', False, PList_Component::GROUP_ACTION);
        $this->groups->setGroupActionParameterName('id');

        $this->session->set('delete_appname', $appname);

        $this->layout->content = new View('backend/groups');
    }
    // }}}
    // {{{ _deleteGroup
    public function _deleteGroup($group)
    {
        if (Model::load('Groups', 'User')->isDeletable($group['id'])) {
            return array( 'label' => _("Delete group"),
                          'uri'   => 'user/backend/applications/delete/group/#id#',
                          'className' => 'delete_action'
                        );
        } else {
            return array( 'label' => _("You cannot delete this group"),
                          'className' => 'delete_action_alt'
                        );
        }
    }
    // }}}
    // {{{ groups_validate_read
    public function groups_validate_read()
    {
        $this->validation->name(0, _("Name"))->add_rules(0, 'required', array($this, '_check_app'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ groups_read_error
    public function groups_read_error()
    {
        $this->_invalid_request("user/backend/applications/index", _("Invalid Application name"));
    }
    // }}}
    // }}}
    // {{{ default_group
    // {{{ default_group_read
    public function default_group_read($appname)
    {
        $this->_default_group($appname);
    }
    // }}}
    // {{{ default_group_validate_read
    public function default_group_validate_read()
    {
        $this->validation->name(0, _("Name"))->add_rules(0, 'required', array($this, '_check_app'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ default_group_read_error
    public function default_group_read_error()
    {
        $this->_invalid_request("user/backend/applications/index", _("Invalid Application name"));
    }
    // }}}
    // }}}
    // {{{ new_group
    // {{{ new_group_read
    public function new_group_read($appname)
    {
        $this->_new_group($appname);
    }
    // }}}
    // {{{ new_group_validate_read
    public function new_group_validate_read()
    {
        $this->validation->name(0, _("Name"))->add_rules(0, 'required', array($this, '_check_app'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ new_group_read_error
    public function new_group_read_error()
    {
        $this->_invalid_request("user/backend/applications/index", _("Invalid Application name"));
    }
    // }}}
    // }}}
    // {{{ users
    // {{{ users_read
    public function users_read($id, $appname)
    {
        $this->global_tabs->setParameter('appname', $appname);
        $this->global_tabs->setParameter('appname', $appname);
        $this->global_tabs->addItem(_("Groups of '%appname%'"), 'user/backend/applications/groups/%appname%', 'user/backend/applications');
        $this->global_tabs->addItem(_("Default Group of '%appname%'"), 'user/backend/applications/default_group/%appname%', 'user/backend/applications');
        $this->global_tabs->additem(_("New Group for '%appname%'"), 'user/backend/applications/new_group/%appname%', 'user/backend/applications');
        $this->global_tabs->additem(_("New User for '%appname%'"), 'user/backend/applications/new_user/%appname%', 'user/backend/applications');
        $this->global_tabs->addItem(_("Users of '%appname%'"), "user/backend/applications/users/".$id."/".$appname, "user/backend/applications");

        $this->_create_users_plist($id, $appname);

        $this->users->addAction("user/backend/applications/app_user_profile/#username#", _("Edit"), 'edit_action');
        $this->users->addAction("user_profile/backend/index/#username#", _("Edit Profile"), 'view_profile');
        $this->users->addAction("user/backend/applications/delete/user/#username#", _("Delete"), 'delete_action');
        $this->users->addAction("user/backend/applications/delete/user", _("Delete"), 'delete_action', False, PList_Component::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username');

        $this->layout->content = new View('backend/users', array("flagsearch"  => false));
    }
    // }}}
    // {{{ users_validate_read
    public function users_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this, '_check_group'));
        $this->validation->name(1, _("Appname"))->add_rules(1, 'required', array($this, '_check_app'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ users_read_error
    public function users_read_error()
    {
        $this->_invalid_request("user/backend/applications/index", _("Invalid ID or Application name"));
    }
    // }}}
    // }}}
    // {{{ all_users
    // {{{ all_users
    public function all_users_any($page = NULL)
    {
        if ($page != Null && preg_match('|^page_users$|', $page)) {
            $user            = $this->session->get('user_user_user');
            $app_name        = $this->session->get('user_user_app');
            $group_name      = $this->session->get('user_user_group');
            $email           = $this->session->get('user_user_email');
            $is_blocked      = $this->session->get('user_user_is_blocked');
            $is_not_verified = $this->session->get('user_user_is_not_verified');
        } else {
            $user            = $this->input->post('user', Null, True);
            $app_name        = $this->input->post('app_name', Null, True);
            $group_name      = $this->input->post('group_name', Null, True);
            $email           = $this->input->post('email', Null, True);
            $is_blocked      = $this->input->post('is_blocked', Null, True);
            $is_not_verified = $this->input->post('is_not_verified', Null, True);
        }

        $this->session->set('user_user_user', $user);
        $this->session->set('user_user_app', $app_name);
        $this->session->set('user_user_group', $group_name);
        $this->session->set('user_user_email', $email);
        $this->session->set('user_user_is_blocked', $is_blocked);
        $this->session->set('user_user_is_not_verified', $is_not_verified);

        $this->_create_users_plist(NULL, $app_name, $group_name, $user, $email, true, $is_blocked, $is_not_verified);

        $this->users->addAction("user/backend/applications/user_profile/#username#", _("Edit"), 'edit_action');
        $this->users->addAction("user_profile/backend/index/#username#", _("Edit Profile"), 'view_profile');
        $this->users->addAction("user/backend/applications/delete/user/#username#", _("Delete"), 'delete_action');
        $this->users->addAction("user/backend/applications/delete/user", _("Delete"), 'delete_action', False, PList_Component::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username');

        $data = array('flagsearch'      => true,
                      'user'            => $user,
                      'app_name'        => $app_name,
                      'group_name'      => $group_name,
                      'email'           => $email,
                      'is_blocked'      => $is_blocked,
                      'is_not_verified' => $is_not_verified,
                      'flagform'        => true);

        $this->layout->content = new View('backend/users', $data);
    }
    // }}}
    // }}}
    // {{{ new_user
    // {{{ new_user_read
    public function new_user_read($appname)
    {
        $this->_new_user($appname);
    }
    // }}}
    // {{{ new_user_validate_read
    public function new_user_validate_read()
    {
        $this->validation->name(0, _("Name"))->add_rules(0, 'required', array($this, '_check_app'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ new_user_read_error
    public function new_user_read_error()
    {
        $this->_invalid_request("user/backend/applications/index", _("Invalid Application name"));
    }
    // }}}
    // }}}
    // {{{ user_profile
    // {{{ app_user_profile
    public function app_user_profile($username)
    {
        $this->user_profile_read($username);
        $this->global_tabs->addItem(_("User's Profile"), 'user/backend/applications/app_user_profile/%username%', 'user/backend/applications');
    }
    // }}}
    // {{{ user_profile_read
    public function user_profile_read($username)
    {
        $this->global_tabs->addItem(_("User's Profile"), 'user/backend/applications/user_profile/%username%', 'user/backend/applications/all_users');
        $this->global_tabs->setParameter('username', $username);
        $this->_user_profile($username);
    }
    // }}}
    // {{{ user_profile_validate_read
    public function user_profile_validate_read()
    {
        $this->validation->name(0, _("Username"))->add_rules(0, 'required', array($this, '_check_user_name_profile_master'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ user_profile_read_error
    public function user_profile_read_error()
    {
        $this->_invalid_request("user/backend/applications/index", _("Invalid Username"));
    }
    // }}}
    // }}}
    // {{{ delete
    // {{{ delete
    public function delete_any($type, $objects = NULL)
    {
        $parent_uri = ($type == 'group') ? 'user/backend/applications' : 'user/backend/applications/all_users';

        if ($objects != NULL) {
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/delete/".$type."/".$objects, $parent_uri);
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
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/delete/".$type, $parent_uri);
        }

        $subjects = array();

        foreach ($objects as $key) {

            $exist = false;

            if (preg_match("/^user$/", $type)) {
                $exist = $this->Users->hasUserName($key, Null, Null, Null);

                if (!$exist) {
                    $this->_invalid_request("user/backend/applications/index", _("Invalid Type"));
                }

                array_push($subjects, $key);

            } else if (preg_match("/^group$/", $type)) {
                $exist = $this->Groups->hasGroup(NULL, NULL, $key);

                if (!$exist) {
                    $this->_invalid_request("user/backend/applications/index", _("Invalid Type"));
                }

                $row = $this->Groups->getGroup($key);
                array_push($subjects, $row['name']);

                $this->global_tabs->setParameter('name', $row['appname']);
                $parent_uri = "user/backend/applications/groups/{$row['appname']}";

            } else {
                $this->_invalid_request("user/backend/applications/index", _("Invalid Object"));
            }

        }

        $appname = $this->session->get_once('delete_appname');

        $subjects = implode(",", $subjects);

        $data = array('objects'    => $objects,
                      'subjects'   => $subjects,
                      'flag'       => $flag,
                      'appname'    => $appname,
                      'flagform'   => true,
                      'parent_uri' => $parent_uri);

        $this->layout->content = new View('backend/delete', $data);
    }
    // }}}
    // {{{ do_delete_any
    public function do_delete_any()
    {
        $flag    = $this->input->post('flag');
        $objects = $this->input->post('objects');

        if ($this->input->post('submit')) {
            if ($flag) {
                $application = $this->input->post('application');
                $this->Groups->deleteGroups($objects, $this->session->get('user.username'));
                url::redirect('user/backend/applications/groups/'.$application);
            } else {
                $this->Users->deleteUsers($objects, NULL, $this->session->get('user.username'));
                url::redirect('user/backend/applications/all_users');
            }
        } else {
            $this->_invalid_request("user/backend/applications/index", _("No form is submitted"));
        }
    }
    // }}}
    // }}}
    // {{{ apps_filters_any
    public function apps_filters_any($page = NULL)
    {
        $applications = new PList_Component('applications');

        if ($page != Null && preg_match('|^page_applications$|', $page)) {
            $name = $this->session->get('user_app_name');
        } else {
            $name = $this->input->post('name', Null, True);
        }

        $this->session->set('user_app_name', $name);

        $applications->setResource($this->Filters->getFilterProperties($name, false));
        $applications->setLimit(Arag_Config::get('limit', 0));
        $applications->addColumn('appname', _("Name"));
        $applications->addColumn('created_by', _("Created By"));
        $applications->addColumn('modified_by', _("Modified By"));
        $applications->addColumn('Filters.getDate', _("Create Date"), PList_Component::VIRTUAL_COLUMN);
        $applications->addColumn('Filters.getModifyDate', _("Modify Date"), PList_Component::VIRTUAL_COLUMN);
        $applications->addAction('user/backend/applications/app_filters/#appname#', _("Edit"), 'edit_action');
        $applications->addAction('Applications_Controller::_delete_action');
        $applications->addAction("user/backend/applications/app_filters_delete/", _("Delete"), 'delete_action', False, PList_Component::GROUP_ACTION);
        $applications->setGroupActionParameterName('appname');

        $this->layout->content = new View('backend/index', array("name" => $name, "flag" => true));
    }
    // }}}
    // {{{ _delete_action
    public static function _delete_action($application)
    {
        $Filters = Model::load('Filters', 'user');

        if ($Filters->isDeletable($application)) {
            return Array( 'uri' => 'user/backend/applications/app_filters_delete/'.$application['appname'],
                          'label' => _("Delete"),
                          'className' => 'delete_action' );
        } else {
            return Array( 'label' => _("Applicaiton is not deletable"),
                          'className' => 'delete_action_alt' );
        }
    }
    // }}}
    // {{{ app_filters
    // {{{ app_filters_read
    public function app_filters_read($appname)
    {
        $this->global_tabs->addItem(_("$appname's Filters"), 'user/backend/applications/app_filters/%appname%', 'user/backend/applications/apps_filters');
        $this->global_tabs->setParameter('appname', $appname);
        $this->_filters_read($appname);
    }
    // }}}
    // {{{ app_filters_write
    public function app_filters_write()
    {
        $filter  = $this->input->post('filter');
        $appname = $this->input->post('application');

        $this->Filters->addFilter($filter, $appname, $this->session->get('user.username'));

        $this->session->set('filter_saved', true);

        $this->app_filters_read($appname);
    }
    // }}}
    // {{{ app_filters_validate_write
    public function app_filters_validate_write()
    {
        $this->validation->name('filter', _("Filters"))->add_rules('filter', 'required', array($this, '_check_filter'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ app_filters_write_error
    public function app_filters_write_error()
    {
        $this->app_filters_read($this->input->post('application'));
    }
    // }}}
    // }}}
    // {{{ filters
    // {{{ _filters_read
    protected function _filters_read($appname)
    {
        $flagsaved = false;

        if ($this->session->get('filter_saved')) {
            $flagsaved = $this->session->get_once('filter_saved');
        }

        $filters_pro = new PList_Component('filters_pro');

        $filters_pro->setResource($this->Filters->getFilterProperties($appname));
        $filters_pro->setLimit(Arag_Config::get('limit', 0));
        $filters_pro->addColumn('appname', _("Name"));
        $filters_pro->addColumn('created_by', _("Created By"));
        $filters_pro->addColumn('modified_by', _("Modified By"));
        $filters_pro->addColumn('Filters.getDate', _("Create Date"), PList_Component::VIRTUAL_COLUMN);
        $filters_pro->addColumn('Filters.getModifyDate', _("Modify Date"), PList_Component::VIRTUAL_COLUMN);

        $filters = new PList_Component('filters');

        $filters->setResource($this->Filters->getFilters($appname));
        $filters->setLimit(Arag_Config::get('limit', 0));
        $filters->addColumn('filter', _("Title"));
        $filters->addColumn('id', Null, PList_Component::HIDDEN_COLUMN);
        $filters->addAction('user/backend/applications/filters_edit/'.$appname.'/#id#', _("Edit"), 'edit_action');
        $filters->addAction('user/backend/applications/filters_delete/'.$appname.'/#id#', _("delete"), 'delete_action');
        $filters->addAction("user/backend/applications/filters_delete/".$appname, _("Delete"), 'delete_action', False, PList_Component::GROUP_ACTION);
        $filters->setGroupActionParameterName('id');

        $this->layout->content = new View('backend/filters', array('appname' => $appname, 'flagsaved' => $flagsaved));
    }
    // }}}
    // {{{ _filters_read_error
    protected function _filters_read_error()
    {
    }
    // }}}
    // {{{ filters_edit_read
    public function filters_edit_read($appname, $id, $flagsaved = false)
    {
        $filters = $this->Filters->getFilters($appname);

        $this->global_tabs->addItem(_("Edit Filters"), 'user/backend/applications/filters_edit/%appname%/%id%', 'user/backend/applications/apps_filters');
        $this->global_tabs->setParameter('id', $id);
        $this->global_tabs->setParameter('appname', $appname);

        $data = array('appname'   => $appname,
                      'id'        => $id,
                      'flagsaved' => $flagsaved,
                      'filter'    => $filters[$id]['filter']);

        $this->layout->content = new View('backend/edit_filters', $data);
    }
    // }}}
    // {{{ filters_edit_write
    public function filters_edit_write()
    {
        $filter  = $this->input->post('filter');
        $appname = $this->input->post('application');
        $id      = $this->input->post('id');

        $this->Filters->editFilter($filter, $id, $appname, $this->session->get('user.username'));

        $this->filters_edit_read($appname, $id, true);
    }
    // }}}
    // {{{ filters_edit_validate_write
    public function filters_edit_validate_write()
    {
        $this->validation->name('filter',_("Filters"))->add_rules('filter', 'required', array($this, '_check_filter'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ filters_edit_write_error
    public function filters_edit_write_error()
    {
        $appname = $this->input->post('application');
        $id      = $this->input->post('id');
        $this->filters_edit_read($appname, $id);
    }
    // }}}
    // {{{ filters_delete_any
    public function filters_delete_any($appname, $objects = NULL)
    {
        // object type clarify that ifyou are going to delete indivual filters
        // or whole application's filters: False means a filter and true means
        // application
        $objecttype = false;

        // check if it is a group action or not
        if ($objects != NULL) {
            //add a sub tab to the global tabs
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/filters_delete/".$appname."/".$objects,
                                                     "user/backend/applications/apps_filters");
            $objects = array($objects);
        } else {
            if ($this->input->post('id')) {
                $objects = $this->input->post('id');
            }
            //add a sub tab to the global tabs
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/filters_delete/".$appname, "user/backend/applications/apps_filters");
        }

        $subjects = array();

        $filters = $this->Filters->getFilters($appname);

        foreach ($objects as $key) {
            $exist = false;

            if ($key < count($filters) || $key == count($filters)) {
                $filter = $filters[$key]['filter'];

                // check if the passed filter(s) exist(s)
                $exist = $this->Filters->hasFilter($appname, $filter);
            }

            if (!$exist) {
                $this->_invalid_request("user/backend/applications/index", _("Invalid Object"));
            }

            array_push($subjects, $filter);
        }

        $subjects = implode(", ", $subjects);

        $data = array('objects'    => $objects,
                      'subjects'   => $subjects,
                      'objecttype' => $objecttype,
                      'appname'    => $appname);

        $this->layout->content = new View('backend/filters_delete', $data);
    }
    // }}}
    // {{{ filters_do_delete_any
    public function filters_do_delete_any()
    {
        $objects    = $this->input->post('objects');
        $objecttype = $this->input->post('objecttype');

        if ($this->input->post('submit')) {
            if ($objecttype) {
                $this->Filters->deleteApps($objects);
                url::redirect('user/backend/applications/apps_filters');
            } else {
                $appname = $this->input->post('application');
                $this->Filters->deleteFilters($objects, $appname);
                url::redirect('user/backend/applications/app_filters/'.$appname);
            }
        } else {
            $this->_invalid_request("user/backend/applications/index", _("No form is submitted"));
        }
    }
    // }}}
    // }}}
    // {{{ app_filters_delete_any
    public function app_filters_delete_any($objects = NULL)
    {
        // object type clarify that ifyou are going to delete indivual filters
        // or whole application's filters: False means a filter and true means
        // application
        $objecttype = true;

        // check if it is a group action or not
        if ($objects != NULL) {
            //add a sub tab to the global tabs
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/app_filters_delete/".$objects, "user/backend/applications/apps_filters");
            $objects = array($objects);
        } else {
            if ($this->input->post('appname')) {
                $objects = $this->input->post('appname');
            }
            //add a sub tab to the global tabs
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/app_filters_delete/", "user/backend/applications/apps_filters");
        }

        $subjects = array();

        foreach ($objects as $key) {
            $exist = false;

            // check if the passed app(s) exist(s)
            $exist = $this->Filters->hasApp($key);

            if (!$exist) {
                $this->_invalid_request("user/backend/applications/apps_filters", _("Invalid Object"));
            }

            array_push($subjects, $key);
        }

        $subjects = implode(", ", $subjects);

        $data = array('objects'    => $objects,
                      'subjects'   => $subjects,
                      'objecttype' => $objecttype,
                      'appname'    => $key);

        $this->layout->content = new View('backend/filters_delete', $data);
    }
    // }}}
    // {{{ add_apps_filters
    // {{{ add_apps_filters_read
    public function add_apps_filters_read()
    {
        $this->layout->content = new View('backend/add_app_filter', array('flagsaved' => $this->session->get_once('app_add_filter_saved')));
    }
    // }}}
    // {{{ add_apps_filters_write
    public function add_apps_filters_write()
    {
        $appname = $this->input->post('appname');
        $this->Filters->addApp($appname, $this->session->get('user.username'));

        $this->session->set('app_add_filter_saved', true);
        url::redirect('user/backend/applications/add_apps_filters');
    }
    // }}}
    // {{{ add_apps_filters_validate_write
    public function add_apps_filters_validate_write()
    {
        $this->validation->name('appname', _("application Name"))->pre_filter('trim', 'appname')
             ->add_rules('appname', 'required', array($this, '_check_app_filter'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ add_apps_filters_write_error
    public function add_apps_filters_write_error()
    {
        $this->add_apps_filters_read();
    }
    // }}}
    // }}}
    // {{{ privileges_parents
    // {{{ privileges_parents_read
    public function privileges_parents_read()
    {
        $this->_create_privileges_list($this->appname, "0");
    }
    // }}}
    // {{{ privileges_parents_write
    public function privileges_parents_write()
    {
        $label     = $this->input->post('newlabel');

        $this->Privileges->addLabel($label, 0, NULL, $this->session->get('user.username'));

        $this->session->set('privileges_add_saved', true);

        $this->privileges_parents_read();
    }
    // }}}
    // {{{ privileges_parents_validate_write
    public function privileges_parents_validate_write()
    {
        $this->validation->name('newlabel', _("Name for new lable"))->pre_filter('trim', 'newlabel')
             ->add_rules('newlabel', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ privileges_parents_write_error
    public function privileges_parents_write_error()
    {
        $this->privileges_parents_read();
    }
    // }}}
    // }}}
    // {{{ privileges_all
    public function privileges_all_any()
    {
        $this->_create_privileges_list($this->appname);
    }
    // }}}
    // {{{ privileges
    // {{{ privileges_read
    public function privileges_read($id)
    {
        $label = $this->Privileges->getLabel($id);
        $this->global_tabs->addItem(_("{$label->label}"), "user/backend/applications/privileges/%id%", "user/backend/applications/privileges_parents");
        $this->global_tabs->setParameter('id', $id);
        $this->_create_privileges_list($this->appname, $id);
    }
    // }}}
    // {{{ privileges_write
    public function privileges_write()
    {
        $label     = $this->input->post('newlabel');
        $privilege = $this->input->post('privilege');
        $parentid  = $this->input->post('parentid');

        $this->Privileges->addLabel($label, $parentid, $privilege, $this->session->get('user.username'));

        $this->session->set('privileges_add_saved', true);

        $this->privileges_read($parentid);
    }
    // }}}
    // {{{ privileges_validate_write
    public function privileges_validate_write()
    {
        $this->validation->name('newlabel', _("Name for new label"))->pre_filter('trim', 'newlabel')
             ->add_rules('newlabel', 'required');

        $this->validation->name('privilege', _("Privilege for new label"))->pre_filter('trim', 'privilege')
             ->add_rules('privilege', 'required', array($this, '_check_privilege'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ privileges_write_error
    public function privileges_write_error()
    {
        $id = $this->input->post('parentid');
        $this->privileges_read($id);
    }
    // }}}
    // }}}
    // {{{ privileges_edit
    // {{{ privileges_edit_read
    public function privileges_edit_read($id)
    {
        $label = $this->Privileges->getLabel($id);

        $this->global_tabs->addItem(_("Edit"), "user/backend/applications/privileges_edit/%id%", "user/backend/applications/privileges_parents");
        $this->global_tabs->setParameter('id', $id);

        $data = array('label'     => $label->label,
                      'privilege' => $label->privilege,
                      'id'        => $id,
                      'parentid'  => $label->parent_id,
                      'flagsaved' => $this->session->get_once('privilege_edited_saved'));

        $this->layout->content = new View('backend/privileges_edit', $data);
    }
    // }}}
    // {{{ privileges_edit_validate_read
    public function  privileges_edit_validate_read()
    {
        $this->validation->name(0, _("Label"))->add_rules(0, 'required', array($this, '_check_label'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ privileges_edit_read_error
    public function privileges_edit_read_error()
    {
        $this->_invalid_request('user/backend/applications/index'. _("Invalid Label"));
    }
    // }}}
    // {{{ privileges_edit_write
    public function privileges_edit_write()
    {
        $label     = $this->input->post('label');
        $id        = $this->input->post('id');
        $privilege = $this->input->post('privilege');

        $this->Privileges->editLabel($label, $id, $privilege, $this->session->get('user.username'));

        $this->session->set('privilege_edited_saved', true);

        $this->privileges_edit_read($id);
    }
    // }}}
    // {{{ privileges_edit_validate_write
    public function privileges_edit_validate_write()
    {
        $this->validation->name('label', _("Label name"))->pre_filter('trim', 'label')
             ->add_rules('label', 'required');

        $this->validation->name('privilege', _("Privilege"))->pre_filter('trim', 'privilege')
             ->add_rules('privilege', array($this, '_check_privilege'));

        $this->validation->name('id', _("id"))->pre_filter('trim', 'id')
             ->add_rules('id', 'required', 'valid::numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ privileges_edit_write_error
    public function privileges_edit_write_error()
    {
        $id = $this->input->post('id');
        $this->privileges_edit_read($id);
    }
    // }}}
    // }}}
    // {{{ privileges_delete
    // {{{ privileges_delete_any
    public function privileges_delete_any($objects = NULL)
    {

        // This flag decides wheter to show a caption message in case the
        // deleting privilege is a parent
        $flagcaption = false;

        // check if it is a group action or not
        if ($objects != NULL) {
            //add a sub tab to the global tabs
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/privileges_delete/".$objects,
                                                     "user/backend/applications/privileges_parents");
            $objects = array($objects);
        } else {
            if ($this->input->post('id')) {
                $objects = $this->input->post('id');
            }
            //add a sub tab to the global tabs
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/privileges_delete/", "user/backend/applications/privileges_parents");
        }

        $subjects = array();

        foreach ($objects as $key) {
            $exist = false;

            // check if the passed app(s) exist(s)
            $exist  = $this->Privileges->hasLabel($key);

            // Fetch label(s) name(s)
            $labels =  $this->Privileges->getLabel($key);

            if ($labels->parent_id === "0") {
                $flagcaption = true;
            }

            if (!$exist) {
                $this->_invalid_request("user/backend/applications/privileges_parents", _("Invalid Object"));
            }

            $subjects[] = $labels->label;
        }

        $subjects = implode(", ", $subjects);

        $data = array('objects'     => $objects,
                      'subjects'    => $subjects,
                      'flagcaption' => $flagcaption);

        $this->layout->content = new View('backend/privileges_delete', $data);
    }
    // }}}
    // {{{  privileges_do_delete_any
    public function privileges_do_delete_any()
    {
        $objects = $this->input->post('objects');

        $this->Privileges->deletePrivileges($objects, $this->session->get('user.username'));

        url::redirect('user/backend/applications/privileges_parents');
    }
    // }}}
    // }}}
    // {{{ group_privileges_edit
    // {{{ group_privileges_edit_read
    public function group_privileges_edit_read($id, $appname)
    {
        $row       = $this->Groups->getGroup($id);
        $groupname = $row['name'];

        $this->global_tabs->setParameter('appname', $appname);
        $this->global_tabs->setParameter('groupname', $groupname);
        $this->global_tabs->setParameter('id', $id);
        $this->global_tabs->addItem(_("Groups of '%appname%'"), 'user/backend/applications/groups/%appname%', 'user/backend/applications');
        $this->global_tabs->addItem(_("Default Group of '%appname%'"), 'user/backend/applications/default_group/%appname%', 'user/backend/applications');
        $this->global_tabs->additem(_("New Group for '%appname%'"), 'user/backend/applications/new_group/%appname%', 'user/backend/applications');
        $this->global_tabs->additem(_("New User for '%appname%'"), 'user/backend/applications/new_user/%appname%', 'user/backend/applications');
        $this->global_tabs->addItem(_("Edit '%groupname%'s' privileges"), 'user/backend/applications/group_privileges_edit/%id%/%appname%', 'user/backend/applications');

        $this->_privileges_edit_read($id, $appname);
    }
    // }}}
    // {{{ group_privileges_edit_write
    public function group_privileges_edit_write()
    {
        $this->_privileges_edit_write($this->input->post('appname'));
    }
    // }}}
    // }}}
    // {{{ new_user
    // {{{ new_user_write
    public function new_user_write()
    {
        $this->_new_user_write($this->input->post('appname'));
    }
    // }}}
    // {{{ new_user_validate_write
    public function new_user_validate_write()
    {
        $passwordLength = Arag_Config::get("passlength", 0);

        $this->validation->name('username', _("Username"))->pre_filter('trim', 'username')
             ->add_rules('username', 'required', 'valid::alpha_dash', array($this, '_check_user_name'), 'length[4, 255]');

        $this->validation->name('password', _("Password"))->pre_filter('trim', 'password')
             ->add_rules('password', 'required', 'length['.$passwordLength.', 255]', 'matches[repassword]');

        $this->validation->name('name', _("Name"))->pre_filter('trim', 'name')
             ->add_rules('name', 'required', 'valid::standard_text');

        $this->validation->name('lastname', _("Last name"))->pre_filter('trim', 'lastame')
             ->add_rules('lastname', 'required', 'valid::standard_text');

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
        $this->new_user_read($this->input->post('appname'));
    }
    // }}}
    // }}}
    // {{{ user_profile
    // {{{ user_profile_write
    public function user_profile_write()
    {
        $this->_user_profile_write($this->input->post('application'));
    }
    // }}}
    // {{{ user_profile_validate_write
    public function user_profile_validate_write()
    {
        $passwordLength = Arag_Config::get("passlength", 0);

        $this->validation->name('password', _("Password"))->add_rules('password', 'matches[repassword]', 'length['.$passwordLength.', 255]');

        $this->validation->name('name', _("Name"))->pre_filter('trim', 'name')
             ->add_rules('name', 'required', 'standard_text');

        $this->validation->name('lastname', _("Last name"))->pre_filter('trim', 'lastname')
             ->add_rules('lastname', 'required', 'standard_text');

        $this->validation->name('email', _("Email"))->pre_filter('trim', 'email')
             ->add_rules('email', 'required', 'valid::email');

        $this->validation->name('groups', _("Group"))->pre_filter('trim', 'groups')
             ->add_rules('groups', 'required');

        return $this->validation->validate();
    }
    // }}}
    // }}}
    // {{{ new_group
    // {{{ new_group_write
    public function new_group_write()
    {
         $this->_new_group_write($this->input->post('appname'), date::get_time('expire_date'));
    }
    // }}}
    // {{{ new_group_validate_write
    public function new_group_validate_write()
    {
        $this->validation->name('newgroup', _("Name for new group"))->pre_filter('trim', 'newgroup')
             ->add_rules('newgroup', 'required', 'alpha_dash', array($this, '_check_group_name'));

        $this->validation->name('expire_date', _("Expire date"))->add_rules('expire_date', 'valid::date[expire_date]');

        return $this->validation->validate();
    }
    // }}}
    // {{{ new_group_write_error
    public function new_group_write_error()
    {
        $this->new_group_read($this->input->post('appname'));
    }
    // }}}
    // }}}
    // {{{ default_group_write
    public function default_group_write()
    {
         $this->_default_group_write($this->input->post('appname'));
    }
    // }}}
    // {{{ settings
    // {{{ settings_read
    public function settings_read()
    {
        $this->_settings_read(true);
    }
    // }}}
    // }}}
    // {{{ password
    // {{{ password_read
    public function password_read()
    {
        $data           = Array();
        $data['length'] = Arag_Config::get("passlength");
        $data['saved']  = $this->session->get_once('user_settings_pass_length_saved');

        $this->layout->content = new View('backend/settings_password', $data);
    }
    // }}}
    // {{{ password_write
    public function password_write()
    {

        Arag_Config::set('passlength', $this->input->post('length'));

        $this->session->set('user_settings_pass_length_saved', true);

        $this->password_read();

    }
    // }}}
    // {{{ password_validate_write
    public function password_validate_write()
    {
        $this->validation->name('length', _("Password Length"))->pre_filter('trim', 'length')
             ->add_rules('length', 'required', 'valid::numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ password_write_error
    public function password_write_error()
    {
        $this->password_read();
    }
    // }}}
    // }}}
    // {{{ expire_time
    // {{{ expire_time_read
    public function expire_time_read()
    {
        $data           = Array();
        $data['expire'] = Arag_Config::get("expire");
        $data['saved']  = $this->session->get_once('user_settings_expire_saved');

        $this->layout->content = new View('backend/settings_expire', $data);
    }
    // }}}
    // {{{ expire_time_write
    public function expire_time_write()
    {

        Arag_Config::set('expire', $this->input->post('expire'));

        $this->session->set('user_settings_expire_saved', true);

        $this->expire_time_read();

    }
    // }}}
    // {{{ expire_time_validate_write
    public function expire_time_validate_write()
    {
        $this->validation->name('expire', _("Expire Time"))->pre_filter('trim', 'expire')
             ->add_rules('expire', 'required', 'valid::numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ expire_time_write_error
    public function expire_time_write_error()
    {
        $this->expire_time_read();
    }
    // }}}
    // }}}
    // {{{ user_blocking
    // {{{ user_blocking_read
    public function user_blocking_read()
    {
        $data                    = Array();
        $data['block_expire']    = Arag_Config::get("block_expire");
        $data['block_counter']   = Arag_Config::get("block_counter");
        $data['captcha_counter'] = Arag_Config::get("captcha_counter");
        $data['saved']           = $this->session->get_once('user_settings_user_blocking_saved');

        $this->layout->content = new View('backend/settings_user_blocking', $data);
    }
    // }}}
    // {{{ user_blocking_write
    public function user_blocking_write()
    {
        Arag_Config::set('block_expire', $this->input->post('block_expire'));
        Arag_Config::set('block_counter', $this->input->post('block_counter'));
        Arag_Config::set('captcha_counter', $this->input->post('captcha_counter'));

        $this->session->set('user_settings_user_blocking_saved', True);

        $this->user_blocking_read();
    }
    // }}}
    // {{{ user_blocking_validate_write
    public function user_blocking_validate_write()
    {
        $block_counter = $this->input->post('block_counter');

        $this->validation->name('block_expire', _("Blocking expire time"))->pre_filter('trim', 'block_expire')
             ->add_rules('block_expire', 'required', 'valid::numeric');

        $this->validation->name('block_counter', _("Blocking attempts"))->pre_filter('trim', 'block_counter')
             ->add_rules('block_counter', 'required', 'valid::numeric');

        $this->validation->name('captcha_counter', _("Captcha attempts"))->pre_filter('trim', 'captcha_counter')
             ->add_rules('captcha_counter', 'required', 'valid::numeric');

        if ((int) $block_counter > 0) {
            $this->validation->message('_less_than', sprintf(_("%s should be less than %d."),
                                       _("Captcha attempts"), $block_counter));
            $this->validation->name('captcha_counter', _("Captcha attempts"))->pre_filter('trim', 'captcha_counter')
                 ->add_rules('captcha_counter', 'required', 'valid::numeric', 'Applications_Controller::_less_than['.$block_counter.']');

        } else {
            $this->validation->name('captcha_counter', _("Captcha attempts"))->pre_filter('trim', 'captcha_counter')
                 ->add_rules('captcha_counter', 'required', 'valid::numeric');
        }

        return $this->validation->validate();
    }
    // }}}
    // {{{ user_blocking_write_error
    public function user_blocking_write_error()
    {
        $this->user_blocking_read();
    }
    // }}}
    // }}}
    // {{{ email_template
    // {{{ email_template_read
    public function email_template_read()
    {
        $data          = Arag_Config::get("email_settings", array(), 'core', False, Kohana::config('arag.master_appname'));
        $data['saved'] = $this->session->get_once('user_settings_email_template_saved');

        $this->layout->content = new View('backend/settings_email_template', $data);
    }
    // }}}
    // {{{ email_template_write
    public function email_template_write()
    {
        $data     = Arag_Config::get("email_settings", array(), 'core', False, Kohana::config('arag.master_appname'));
        $settings = array (
                            'subject'  => $this->input->post('subject'),
                            'template' => $this->input->post('template')
                          );

        // Merge with core settings
        $settings = array_merge($settings, $data);

        Arag_Config::set('email_settings', $settings, 'core', Kohana::config('arag.master_appname'));

        $this->session->set('user_settings_email_template_saved', true);
        $this->email_template_read();
    }
    // }}}
    // {{{ email_template_validate_write
    public function email_template_validate_write()
    {
        $this->validation->name('subject', _("Subject"))->pre_filter('trim', 'subject')
             ->add_rules('subject', 'required');

        $this->validation->name('template', _("Email's template"))->pre_filter('trim', 'template')
             ->add_rules('template', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ email_template_write_error
    public function email_template_write_error()
    {
        $this->email_template_read();
    }
    // }}}
    // }}}
    // {{{ add
    // {{{ add_read
    public function add_read()
    {
        $this->layout->content               = New View('backend/add_app');
        $this->layout->content->name         = $this->input->post('name');
        $this->layout->content->applications = $this->Applications->getApps();
    }
    // }}}
    // {{{ add_validate_write
    public function add_validate_write()
    {
        $this->validation->name('name', _("Name"))->add_rules('name', 'required', 'alpha_dash');
        if ($this->Applications->hasApp($this->input->post('name'))) {
            $this->validation->message('already_exists', _("An application with name %s already exists.Please choose another name."));
            $this->validation->add_error('name', 'already_exists');
        }
        return $this->validation->validate();
    }
    // }}}
    // {{{ add_write_error
    public function add_write_error()
    {
        $this->add_read();
    }
    // }}}
    // {{{ add_write
    public function add_write()
    {
        $name     = $this->input->post('name');
        $author   = $this->session->get('user.username');
        $template = $this->input->post('template');

        $this->Applications->addApp($name, $author, 'admin', 1, $template);
        url::redirect('user/backend/applications');
    }
    // }}}
    // }}}
    // {{{ privileges_deploy
    // {{{ privileges_deploy_read
    public function privileges_deploy_read()
    {
        $selected_apps = $this->input->post('applications');
        $app_groups    = Array();

        if ($selected_apps) {
            // We are in validation error stage
            foreach ($selected_apps as $appname) {

                $_appname = ($appname == '_all_') ? Null : $appname;
                $groups   = $this->Groups->getGroupsName($_appname);

                $app_groups[$appname] = Array('_all_' => _("All"));
                $app_groups[$appname] = array_merge($app_groups[$appname], array_combine($groups, $groups));
            }
        }

        $applications  = $this->Applications->getApps();
        $_applications = Array();

        $parent_labels = $this->Privileges->getParentLabels();

        foreach ($applications as $application) {
            $_applications[] = $application['name'];
        }

        $this->layout->content                = new View('backend/privileges_deploy');
        $this->layout->content->app_labels    = array_combine($_applications, $_applications);
        $this->layout->content->parent_labels = $parent_labels;
        $this->layout->content->app_groups    = $app_groups;
        $this->layout->content->success       = $this->session->get_once('privileges_deploy_success', False);
    }
    // }}}
    // {{{ privileges_deploy_write
    public function privileges_deploy_write()
    {
        $applications = $this->input->post('applications');
        $groups       = $this->input->post('groups');
        $parent       = $this->input->post('parent');
        $label        = $this->input->post('label');
        $privilege    = $this->input->post('privilege');

        while (($key = array_search('_all_', $applications)) !== False) {

            $all_apps = $this->Applications->getAppsName();
            $group    = $groups[$key];

            unset($applications[$key]);
            unset($groups[$key]);

            $applications = array_merge($applications, $all_apps);
            $apps_count   = count($applications);
            $groups       = array_pad($groups, $apps_count, $group);
        }

        while (($key = array_search('_all_', $groups)) !== False) {

            $all_groups  = $this->Groups->getGroupsName();
            $application = $applications[$key];

            unset($applications[$key]);
            unset($groups[$key]);

            $groups       = array_merge($groups, $all_groups);
            $groups_count = count($groups);
            $applications = array_pad($applications, $groups_count, $application);
        }

        $result = $this->Privileges->getLabelsByPrivilege($privilege, $parent);

        if (empty($result)) {
            $id = $this->Privileges->addLabel($label, $parent, $privilege, $this->session->get('user.username'));
        } else {
            $result = current($result);
            $id     = (int) $result['id'];
        }

        foreach ($applications as $key => $appname) {

            if ($appname == '_all_') {
                continue;
            }

            $group = $this->Groups->getGroup(Null, $appname, $groups[$key]);

            if (isset($group['id'])) {
                $subpris     = $this->Privileges->getAppPrivileges($appname);
                $allselected = $this->Privileges->getPrivileges($group['id'], true);
                $ids         = $this->Privileges->getSelectedPrivileges($subpris, $allselected, True);
                $ids[]       = $id;

                $this->Privileges->editPrivileges($ids, $group['id'], $appname);
            }
        }

        $this->session->set_flash('privileges_deploy_success', True);
        url::redirect('user/backend/applications/privileges_deploy');
    }
    // }}}
    // {{{ privileges_deploy_validate_write
    public function privileges_deploy_validate_write()
    {
        $this->validation->name('applications.*', _("Applications"))->add_rules('applications', 'required');
        $this->validation->name('groups.*', _("Applications"))->add_rules('groups', 'required');
        $this->validation->name('parent', _("Parent"))->add_rules('parent', 'required');
        $this->validation->name('label', _("Label"))->pre_filter('trim', 'newlabel')->add_rules('label', 'required');
        $this->validation->name('privilege', _("Privilege"))->pre_filter('trim', 'privilege')
             ->add_rules('privilege', 'required', array($this, '_check_privilege'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ privileges_deploy_write_error
    public function privileges_deploy_write_error()
    {
        $this->privileges_deploy_read();
    }
    // }}}
    // }}}
    // {{{ get_groups_of
    public function get_groups_of($appname = Null)
    {
        $entries = Array();

        if ($appname) {

            $_appname = ($appname == '_all_') ? Null : $appname;
            $groups   = $this->Groups->getGroupsName($_appname);

            foreach ($groups as $group) {
                $entries[] = Array('key' => $group, 'value' => $group);
            }
        }

        $this->layout = new View('backend/groups_list');
        $this->layout->json = json_encode(Array('entries' => $entries));
    }
    // }}}
}
