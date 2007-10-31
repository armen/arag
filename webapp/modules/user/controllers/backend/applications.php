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
        $this->global_tabs->addItem(_("Filters"), 'user/backend/applications/apps_filters');
        $this->global_tabs->addItem(_("Filters"), 'user/backend/applications/apps_filters', 'user/backend/applications/apps_filters');
        $this->global_tabs->addItem(_("Add Application"), 'user/backend/applications/add_apps_filters', 'user/backend/applications/apps_filters');
        $this->global_tabs->addItem(_("Privileges"), 'user/backend/applications/privileges_parents');
        $this->global_tabs->addItem(_("Parents"), 'user/backend/applications/privileges_parents', 'user/backend/applications/privileges_parents');
        $this->global_tabs->addItem(_("All"), 'user/backend/applications/privileges_all', 'user/backend/applications/privileges_parents');
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
        
        $this->load->vars(array("name" => $name,
                                "flag" => false));
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
        $this->groups->addColumn('created_by', _("Created By"));
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
        $row  = $this->GroupsModel->getAllAppGroups($appname);
        $row2 = $this->GroupsModel->getDefaultGroup($appname);

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
            
        $this->GroupsModel->newGroup($appname, $newgroup);

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
    function new_user_read($appname)
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

        $this->session->set_userdata('new_user_saved', true);

        redirect('user/backend/applications/new_user/'.$appname);
        
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
        $this->global_tabs->addItem(_("User's Profile"), "user/backend/applications/user_profile/%username%"); 
        $this->global_tabs->setParameter('username', $username);

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
                                "blocked"      => $userprofile['blocked']));

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
        $blocked   = $this->input->post('blocked', true);

        $this->UsersModel->editUser($appname, $email, $name, $lastname, $groupname, $username, $password, $blocked);

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
            } else {
                $this->_invalid_request("user/backend/applications/index");                
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
    // {{{ do_delete
    function do_delete()
    {
        $flag    = $this->input->post('flag');
        $objects = $this->input->post('objects');
         
        if ($this->input->post('submit')) {
            if ($flag) {
                $application = $this->input->post('application');
                $this->GroupsModel->deleteGroups($objects);
                redirect('user/backend/applications/groups/'.$application);
            } else {
                $this->UsersModel->deleteUsers($objects);
                redirect('user/backend/applications/all_users');
            }
        } else {
            $this->_invalid_request("user/backend/applications/index");
        }

        

    }
    // }}}
    //{{{ apps_filters
    function apps_filters($page = NULL)
    {
        $this->load->component('PList', 'applications');

        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {        
            $name = $this->session->userdata('user_app_name');
        } else {
            $name = $this->input->post('name', True);
        }

        $this->session->set_userdata('user_app_name', $name);

        $this->applications->setResource($this->FiltersModel->getFilterProperties($name, false));
        $this->applications->setLimit($this->config->item('limit', NULL, 0));
        $this->applications->addColumn('appname', _("Name"));        
        $this->applications->addColumn('created_by', _("Created By"));
        $this->applications->addColumn('modified_by', _("Modified By"));
        $this->applications->addColumn('FiltersModel.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->applications->addColumn('FiltersModel.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);      
        $this->applications->addAction('user/backend/applications/app_filters/#appname#', _("Edit"), 'edit_action');
        $this->applications->addAction('user/backend/applications/app_filters_delete/#appname#', _("Delete"), 'delete_action', 'FiltersModel.isDeletable');
        $this->applications->addAction("user/backend/applications/app_filters_delete/", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->applications->setGroupActionParameterName('appname');
        
        $this->load->vars(array("name" => $name,
                                "flag" => true));
        $this->load->view('backend/index');
    }
    //}}}
    //{{{ app_filters_read
    function app_filters_read($appname)
    {
        $this->global_tabs->addItem(_("$appname's Filters"), 'user/backend/applications/app_filters/%appname%', 'user/backend/applications/apps_filters');
        $this->global_tabs->setParameter('appname', $appname);
        $this->_filters_read($appname);
    }
    //}}}
    //{{{ app_filters_write
    function app_filters_write()
    {
        $filter  = $this->input->post('filter');
        $appname = $this->input->post('application');

        $this->FiltersModel->addFilter($filter, $appname);
        
        $this->session->set_userdata('filter_saved', true);

        $this->app_filters_read($appname);
    }
    //}}}
    //{{{ app_filters_write_error
    function app_filters_write_error()
    {
        $this->app_filters_read($this->input->post('application'));
    }
    //}}}
    //{{{ _filters_read
    function _filters_read($appname)
    {   
        $flagsaved = false;

        if ($this->session->userdata('filter_saved')) {
            $flagsaved = $this->session->userdata('filter_saved');
            $this->session->unset_userdata('filter_saved');
        }

        $this->load->component('PList', 'filters_pro');

        $this->filters_pro->setResource($this->FiltersModel->getFilterProperties($appname));
        $this->filters_pro->setLimit($this->config->item('limit', NULL, 0));
        $this->filters_pro->addColumn('appname', _("Name"));        
        $this->filters_pro->addColumn('created_by', _("Created By"));
        $this->filters_pro->addColumn('modified_by', _("Modified By"));
        $this->filters_pro->addColumn('FiltersModel.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->filters_pro->addColumn('FiltersModel.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);

        $this->load->component('PList', 'filters');

        $this->filters->setResource($this->FiltersModel->getFilters($appname));
        $this->filters->setLimit($this->config->item('limit', NULL, 0));
        $this->filters->addColumn('filter', _("Title"));        
        $this->filters->addColumn('id', Null, PList::HIDDEN_COLUMN);
        $this->filters->addAction('user/backend/applications/filters_edit/'.$appname.'/#id#', _("Edit"), 'edit_action');
        $this->filters->addAction('user/backend/applications/filters_delete/'.$appname.'/#id#', _("delete"), 'delete_action');
        $this->filters->addAction("user/backend/applications/filters_delete/".$appname, _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->filters->setGroupActionParameterName('id');
        
        $this->load->vars(array('appname'   => $appname,
                                'flagsaved' => $flagsaved));

        $this->load->view('backend/filters');           
    }
    //}}}
    //{{{ _filters_read_error
    function _filters_read_error()
    {
        
    }
    //}}}
    //{{{ filters_edit_read
    function filters_edit_read($appname, $id, $flagsaved = false)
    {
        $filters = $this->FiltersModel->getFilters($appname);

        $this->global_tabs->addItem(_("Edit Filters"), 'user/backend/applications/filters_edit/%name%/%id%', 'user/backend/applications/apps_filters');
        $this->global_tabs->setParameter('id', $id);
        $this->global_tabs->setParameter('name', $appname);

        $this->load->vars(array('appname'   => $appname,
                                'id'        => $id,
                                'flagsaved' => $flagsaved,
                                'filter'    => $filters[$id]['filter']));

        $this->load->view('backend/edit_filters');
    }
    //}}}
    //{{{ filters_edit_write
    function filters_edit_write()
    {
        $filter  = $this->input->post('filter');
        $appname = $this->input->post('application');
        $id      = $this->input->post('id');

        $this->FiltersModel->editFilter($filter, $id, $appname);

        $this->filters_edit_read($appname, $id, true);
    }
    //}}}
    //{{{ filters_edit_write_error
    function filters_edit_write_error()
    {   
        $appname = $this->input->post('application');
        $id      = $this->input->post('id');
        $this->filters_edit_read($appname, $id);
    }
    //}}}
    // {{{ filters_delete
    function filters_delete($appname, $objects = NULL)
    {
        // object type clarify that ifyou are going to delete indivual filters
        // or whole application's filters: False means a filter and true means
        // application
        $objecttype = false;

        // check if it is a group action or not
        if ($objects != NULL) {
            //add a sub tab to the global tabs
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/filters_delete/".$appname."/".$objects, "user/backend/applications/apps_filters");
            $objects = array($objects);
        } else {
            if ($this->input->post('id')) {
                $objects = $this->input->post('id');
            }
            //add a sub tab to the global tabs
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/filters_delete/".$appname, "user/backend/applications/apps_filters");
        }

        $subjects = array();

        $filters = $this->FiltersModel->getFilters($appname);

        foreach ($objects as $key) {
            $exist = false;

            if ($key < count($filters) || $key == count($filters)) {
                $filter = $filters[$key]['filter']; 

                // check if the passed filter(s) exist(s)
                $exist = $this->FiltersModel->hasFilter($appname, $filter);
            }
                
            if (!$exist) {
                $this->_invalid_request("user/backend/applications/index");
            }

            array_push($subjects, $filter);
        }
        
        $subjects = implode(", ", $subjects);
        
        $this->load->vars(array('objects'    => $objects,
                                'subjects'   => $subjects,
                                'objecttype' => $objecttype,
                                'appname'    => $appname));

        $this->load->view('backend/filters_delete');
    }
    // }}}
    // {{{ app_filters_delete
    function app_filters_delete($objects = NULL)
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
            $exist = $this->FiltersModel->hasApp($key);
                
            if (!$exist) {
                $this->_invalid_request("user/backend/applications/apps_filters");
            }

            array_push($subjects, $key);
        }
        
        $subjects = implode(", ", $subjects);
        
        $this->load->vars(array('objects'    => $objects,
                                'subjects'   => $subjects,
                                'objecttype' => $objecttype,
                                'appname'    => $key));

        $this->load->view('backend/filters_delete');
    }
    // }}} 
    // {{{ filters_do_delete
    function filters_do_delete()
    {
        $objects    = $this->input->post('objects');
        $objecttype = $this->input->post('objecttype');
         
        if ($this->input->post('submit')) {
            if ($objecttype) {
                $this->FiltersModel->deleteApps($objects);
                redirect('user/backend/applications/apps_filters');
            } else {
                $appname = $this->input->post('application');
                $this->FiltersModel->deleteFilters($objects, $appname);
                redirect('user/backend/applications/app_filters/'.$appname);
            }
        } else {
            $this->_invalid_request("user/backend/applications/index");
        }
    }
    // }}}
    // {{{ add_apps_filters_read
    function add_apps_filters_read()
    {
        $flagsaved = false;
        
        if ($this->session->userdata('app_add_filter_saved')) {
            $flagsaved = true;
            $this->session->unset_userdata('app_add_filter_saved');
        }
        
        $this->load->vars(array('flagsaved' => $flagsaved));

        $this->load->view('backend/add_app_filter');

    }
    // }}}
    // {{{ add_apps_filters_write
    function add_apps_filters_write()
    {
        $appname = $this->input->post('appname');
        $this->FiltersModel->addApp($appname);

        $this->session->set_userdata('app_add_filter_saved', true);
        redirect('user/backend/applications/add_apps_filters');
    }
    // }}}
    // {{{ add_apps_filters_write_error
    function add_apps_filters_write_error()
    {
        $this->add_apps_filters_read();
    }
    // }}}
    // {{{ privileges_parents_read
    function privileges_parents_read()
    {
        $this->_create_privileges_list("_master_", "0");
    }
    // }}}
    // {{{ privileges_parents_write
    function privileges_parents_write()
    {
        $label     = $this->input->post('newlabel');
        $privilege = $this->input->post('privilege');

        $this->PrivilegesModel->addLabel($label, 0, $privilege);

        $this->session->set_userdata('privileges_add_saved', true);
        
        $this->privileges_parents_read();
    }
    // }}}
    // {{{ privileges_parents_write_error
    function privileges_parents_write_error()
    {
        $this->privileges_parents_read();
    }
    // }}}
    // {{{ privileges_all
    function privileges_all()
    {
        $this->_create_privileges_list("_master_");
    }
    // }}}
    // {{{ privileges_read
    function privileges_read($id)
    {
        $label = $this->PrivilegesModel->getLabel($id);
        $this->global_tabs->addItem(_("$label->label"), "user/backend/applications/privileges/%id%", "user/backend/applications/privileges_parents");
        $this->global_tabs->setParameter('id', $id);
        $this->_create_privileges_list("_master_", $id);
    }
    // }}}
    // {{{ privileges_write
    function privileges_write()
    {
        $label     = $this->input->post('newlabel');
        $privilege = $this->input->post('privilege');
        $parentid  = $this->input->post('parentid');

        $this->PrivilegesModel->addLabel($label, $parentid, $privilege);

        $this->session->set_userdata('privileges_add_saved', true);
        
        $this->privileges_read($parentid);
    }
    // }}}
    // {{{ privileges_write_error
    function privileges_write_error()
    {   
        $id = $this->input->post('parentid');
        $this->privileges_read($id);
    }
    // }}}
    // {{{ privileges_edit_read
    function privileges_edit_read($id)
    {
        $flagsaved = false;
        if ($this->session->userdata('privilege_edited_saved')) {
            $flagsaved = $this->session->userdata('privilege_edited_saved');
            $this->session->userdata('privilege_edited_saved');
        }
        
        $label    = $this->PrivilegesModel->getLabel($id);
            
        $this->global_tabs->addItem(_("Edit"), "user/backend/applications/privileges_edit/%id%", "user/backend/applications/privileges_parents");
        $this->global_tabs->setParameter('id', $id);

        $this->load->vars(array('label'     => $label->label,
                                'privilege' => $label->privilege,
                                'id'        => $id,
                                'flagsaved' => $flagsaved));

        $this->load->view('backend/privileges_edit');
    }
    // }}}
    // {{{ privileges_edit_read_error
    function privileges_edit_read_error()
    {
        $this->_invalid_request('user/backend/applications/index');
    }
    // }}}
    // {{{ privileges_edit_write
    function privileges_edit_write()
    {
        $label     = $this->input->post('label');
        $id        = $this->input->post('id');
        $privilege = $this->input->post('privilege');

        $this->PrivilegesModel->editLabel($label, $id, $privilege);

        $this->session->set_userdata('privilege_edited_saved', true);

        $this->privileges_edit_read($id);
    }
    // }}}
    // {{{ privileges_edit_write_error
    function privileges_edit_write_error()
    {
        $id = $this->input->post('id');
        $this->privileges_edit_read($id);
    }
    // }}}
}
?>
