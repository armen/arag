<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Applications_Controller extends Backend_Controller 
{   
    // {{{ Constructor
    function __construct()
    {   
        parent::__construct();

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
        $this->global_tabs->addItem(_("Page Limit"), 'user/backend/applications/settings', 'user/backend/applications/settings');
        $this->global_tabs->addItem(_("Password Settings"), 'user/backend/applications/password', 'user/backend/applications/settings');
        $this->global_tabs->addItem(_("Expire Time"), 'user/backend/applications/expire_time', 'user/backend/applications/settings');
        $this->global_tabs->addItem(_("User Blocking"), 'user/backend/applications/user_blocking', 'user/backend/applications/settings');
    }
    // }}}
    // {{{ index
    public function index($page = Null)
    {
        $applications = new PList_Component('applications');

        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {        
            $name = $this->session->get('user_app_name');
        } else {
            $name = $this->input->post('name', True);
        }

        $this->session->set('user_app_name', $name);

        $applications->setResource($this->Applications->getApps($name));
        $applications->setLimit(Arag_Config::get('limit', 0));
        $applications->addColumn('name', _("Name"));        
        $applications->addColumn('default_group', _("Default Group"));
        $applications->addColumn('Applications.getDate', _("Create Date"), PList_Component::VIRTUAL_COLUMN);
        $applications->addAction('user/backend/applications/groups/#name#', _("Edit"), 'edit_action');      
        
        $this->layout->content = new View('backend/index', array("name" => $name, "flag" => false)); 
    }
    // }}}
    // {{{ index_error
    public function index_error()
    {
        $this->index();
    }
    // }}}
    // {{{ groups_read
    public function groups_read($appname)
    {
        $this->_create_groups_list($appname);

        $this->groups->addAction('user/backend/applications/group_privileges_edit/#id#/'.$appname, _("Privileges"), 'privileges_action');
        $this->groups->addAction('user/backend/applications/users/#id#/'.$appname, _("View"), 'view_action'); 
        $this->groups->addAction('user/backend/applications/delete/group/#id#', _("Delete"), 'delete_action');
        $this->groups->addAction("user/backend/applications/delete/group", _("Delete"), 'delete_action', PList_Component::GROUP_ACTION);
        $this->groups->setGroupActionParameterName('id');

        $this->session->set('delete_appname', $appname);

        $this->layout->content = new View('backend/groups'); 
    }
    // }}}
    // {{{ groups_read_error
    public function groups_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");        
    }
    // }}}
    // {{{ default_group_read
    public function default_group_read($appname)
    {
        $this->_default_group($appname);
    }
    // }}}
    // {{{ default_group_read_error
    public function default_group_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");
    }
    // }}}
    // {{{ new_group_read
    public function new_group_read($appname)
    {
        $this->_new_group($appname);
    }
    // }}}
    // {{{ new_group_read_error
    public function new_group_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");
    }
    // }}}
    // {{{ users_read
    public function users_read($id, $appname)
    {
        $this->global_tabs->setParameter('name', $appname);
 
        $this->global_tabs->addItem(_("Users"), "user/backend/applications/users/".$id."/".$appname, "user/backend/applications");
        
        $this->_create_users_plist($id);

        $this->users->addAction("user/backend/applications/user_profile/#username#", _("Edit"), 'edit_action');
        $this->users->addAction("user/backend/applications/delete/user/#username#", _("Delete"), 'delete_action');
        $this->users->addAction("user/backend/applications/delete/user", _("Delete"), 'delete_action', PList_Component::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username');
        
        $this->layout->content = new View('backend/users', array("flagsearch"  => false));
    }
    // }}}
    // {{{ users_read_error
    public function users_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");
    }
    // }}}
    // {{{ all_users
    public function all_users($page = NULL)
    {
        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {        
            $user       = $this->session->get('user_user_user');
            $app_name   = $this->session->get('user_user_app');
            $group_name = $this->session->get('user_user_group');
        } else {
            $user       = $this->input->post('user', True);
            $app_name   = $this->input->post('app_name', True);
            $group_name = $this->input->post('group_name', True);
        }

        $this->session->set('user_user_user', $user);
        $this->session->set('user_user_app', $app_name);
        $this->session->set('user_user_group', $group_name);
        
        $this->_create_users_plist(NULL, $app_name, $group_name, $user);

        $this->users->addAction("user/backend/applications/user_profile/#username#", _("Edit"), 'edit_action');
        $this->users->addAction("user/backend/applications/delete/user/#username#", _("Delete"), 'delete_action');
        $this->users->addAction("user/backend/applications/delete/user", _("Delete"), 'delete_action', PList_Component::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username');
        
        $data = array("flagsearch" => true,
                      "user"       => $user,
                      "app_name"   => $app_name,
                      "group_name" => $group_name,
                      "flagform"   => true);

        $this->layout->content = new View('backend/users', $data);    
    }
    // }}}
    // {{{ new_user_read
    public function new_user_read($appname)
    {
        $this->_new_user($appname);      
    }
    // }}}
    // {{{ new_user_read_error
    public function new_user_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");
    }
    // }}}
    // {{{ user_profile_read
    public function user_profile_read($username)
    {
        $this->global_tabs->addItem(_("User's Profile"), "user/backend/applications/user_profile/%username%"); 
        $this->global_tabs->setParameter('username', $username);
        $this->_user_profile($username);   
    }
    // }}}
    // {{{ user_profile_read_error
    public function user_profile_read_error()
    {
        $this->_invalid_request("user/backend/applications/index");
    }
    // }}}
    // {{{ delete
    public function delete($type, $objects = NULL)
    {
        if ($objects != NULL) {
            $this->global_tabs->addItem(_("Delete"), "user/backend/applications/delete/".$type."/".$objects, "user/backend/applications");
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
                $exist = $this->Users->hasUserName($key);
                
                if (!$exist) {
                    $this->_invalid_request("user/backend/applications/index");
                }
                
                array_push($subjects, $key);
            } else if (preg_match("/^group$/", $type)) {
                $exist = $this->Groups->hasGroup(NULL, NULL, $key);
                
                if (!$exist) {
                    $this->_invalid_request("user/backend/applications/index");
                }
                
                $row = $this->Groups->getGroup($key);
                array_push($subjects, $row['name']);
            } else {
                $this->_invalid_request("user/backend/applications/index");                
            }

        }
        
        $appname = $this->session->get_once('delete_appname');

        $subjects = implode(",", $subjects);
        
        $data = array('objects'  => $objects,
                      'subjects' => $subjects,
                      'flag'     => $flag,
                      'appname'  => $appname,
                      'flagform' => true);

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
                url::redirect('user/backend/applications/groups/'.$application);
            } else {
                $this->Users->deleteUsers($objects, NULL, $this->session->get('user.username'));
                url::redirect('user/backend/applications/all_users');
            }
        } else {
            $this->_invalid_request("user/backend/applications/index");
        }
    }
    // }}} 
    //{{{ apps_filters
    public function apps_filters($page = NULL)
    {
        $applications = new PList_Component('applications');

        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {        
            $name = $this->session->get('user_app_name');
        } else {
            $name = $this->input->post('name', True);
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
        $applications->addAction('user/backend/applications/app_filters_delete/#appname#', _("Delete"), 'delete_action', 'Filters.isDeletable');
        $applications->addAction("user/backend/applications/app_filters_delete/", _("Delete"), 'delete_action', PList_Component::GROUP_ACTION);
        $applications->setGroupActionParameterName('appname');
        
        $this->layout->content = new View('backend/index', array("name" => $name, "flag" => true));
    }
    //}}}
    //{{{ app_filters_read
    public function app_filters_read($appname)
    {
        $this->global_tabs->addItem(_("$appname's Filters"), 'user/backend/applications/app_filters/%appname%', 'user/backend/applications/apps_filters');
        $this->global_tabs->setParameter('appname', $appname);
        $this->_filters_read($appname);
    }
    //}}}
    //{{{ app_filters_write
    public function app_filters_write()
    {
        $filter  = $this->input->post('filter');
        $appname = $this->input->post('application');

        $this->Filters->addFilter($filter, $appname, $this->session->get('user.username'));
        
        $this->session->set('filter_saved', true);

        $this->app_filters_read($appname);
    }
    //}}}
    //{{{ app_filters_write_error
    public function app_filters_write_error()
    {
        $this->app_filters_read($this->input->post('application'));
    }
    //}}}
    //{{{ _filters_read
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
        $filters->addAction("user/backend/applications/filters_delete/".$appname, _("Delete"), 'delete_action', PList_Component::GROUP_ACTION);
        $filters->setGroupActionParameterName('id');
        
        $this->layout->content = new View('backend/filters', array('appname' => $appname, 'flagsaved' => $flagsaved));
    }
    //}}}
    //{{{ _filters_read_error
    protected function _filters_read_error()
    {
        
    }
    //}}}
    //{{{ filters_edit_read
    public function filters_edit_read($appname, $id, $flagsaved = false)
    {
        $filters = $this->Filters->getFilters($appname);

        $this->global_tabs->addItem(_("Edit Filters"), 'user/backend/applications/filters_edit/%name%/%id%', 'user/backend/applications/apps_filters');
        $this->global_tabs->setParameter('id', $id);
        $this->global_tabs->setParameter('name', $appname);

        $data = array('appname'   => $appname,
                      'id'        => $id,
                      'flagsaved' => $flagsaved,
                      'filter'    => $filters[$id]['filter']);

        $this->layout->content = new View('backend/edit_filters', $data);
    }
    //}}}
    //{{{ filters_edit_write
    public function filters_edit_write()
    {
        $filter  = $this->input->post('filter');
        $appname = $this->input->post('application');
        $id      = $this->input->post('id');

        $this->Filters->editFilter($filter, $id, $appname, $this->session->get('user.username'));

        $this->filters_edit_read($appname, $id, true);
    }
    //}}}
    //{{{ filters_edit_write_error
    public function filters_edit_write_error()
    {   
        $appname = $this->input->post('application');
        $id      = $this->input->post('id');
        $this->filters_edit_read($appname, $id);
    }
    //}}}
    // {{{ filters_delete
    public function filters_delete($appname, $objects = NULL)
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
                $this->_invalid_request("user/backend/applications/index");
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
    // {{{ app_filters_delete
    public function app_filters_delete($objects = NULL)
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
                $this->_invalid_request("user/backend/applications/apps_filters");
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
    // {{{ filters_do_delete
    public function filters_do_delete()
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
            $this->_invalid_request("user/backend/applications/index");
        }
    }
    // }}}
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
    // {{{ add_apps_filters_write_error
    public function add_apps_filters_write_error()
    {
        $this->add_apps_filters_read();
    }
    // }}}
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
    // {{{ privileges_parents_write_error
    public function privileges_parents_write_error()
    {
        $this->privileges_parents_read();
    }
    // }}}
    // {{{ privileges_all
    public function privileges_all()
    {
        $this->_create_privileges_list($this->appname);
    }
    // }}}
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
    // {{{ privileges_write_error
    public function privileges_write_error()
    {   
        $id = $this->input->post('parentid');
        $this->privileges_read($id);
    }
    // }}}
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
    // {{{ privileges_edit_read_error
    public function privileges_edit_read_error()
    {
        $this->_invalid_request('user/backend/applications/index');
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
    // {{{ privileges_edit_write_error
    public function privileges_edit_write_error()
    {
        $id = $this->input->post('id');
        $this->privileges_edit_read($id);
    }
    // }}}
    // {{{ privileges_delete
    public function privileges_delete($objects = NULL)
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
                $this->_invalid_request("user/backend/applications/privileges_parents");
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
    // {{{  privileges_do_delete
    public function privileges_do_delete()
    {
        $objects = $this->input->post('objects');
        
        $this->Privileges->deletePrivileges($objects, $this->session->get('user.username'));

        url::redirect('user/backend/applications/privileges_parents');
    }
    // }}}
    //{{{ group_privileges_edit_read
    public function group_privileges_edit_read($id, $appname)
    {
        $row  = $this->Groups->getGroup($id);
        $name = $row['name'];
        $this->global_tabs->setParameter('name', $appname);
 
        $this->global_tabs->addItem(_("Edit $name's privileges"), "user/backend/applications/group_privileges_edit/".$id."/".$appname, 
                                                                  "user/backend/applications");
        $this->_privileges_edit_read($id, $appname);
    }
    //}}}
    //{{{ group_privileges_edit_write
    public function group_privileges_edit_write()
    {
        $this->_privileges_edit_write($this->input->post('appname'));
    }
    //}}}
    // {{{ new_user_write
    public function new_user_write()
    {
        $this->_new_user_write($this->input->post('appname'));
    }
    // }}}
    // {{{ new_user_write_error
    public function new_user_write_error()
    {
        $this->new_user_read($this->input->post('appname'));
    }
    // }}}
    // {{{ user_profile_write
    public function user_profile_write()
    {
        $this->_user_profile_write($this->input->post('application'));
    }
    // }}}
    // {{{ new_group_write
    public function new_group_write()
    {
         $this->_new_group_write($this->input->post('appname'));              
    }
    // }}}
    // {{{ new_group_write_error
    public function new_group_write_error()
    {
        $this->new_group_read($this->input->post('appname'));
    }
    // }}} 
    // {{{ default_group_write
    public function default_group_write()
    {
         $this->_default_group_write($this->input->post('appname'));
    }
    // }}}
    // {{{ settings_read
    public function settings_read()
    {
        $this->_settings_read(true);
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
    // {{{ settings_write_error
    public function settings_write_error()
    {
        $this->settings_read();
    }
    // }}}
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
    // {{{ password_write_error
    public function password_write_error()
    {
        $this->password_read();
    }
    // }}}
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
    // {{{ expire_time_write_error
    public function expire_time_write_error()
    {
        $this->expire_time_read();
    }
    // }}}
    // {{{ user_blocking_read
    public function user_blocking_read()
    {   
        $data                  = Array();
        $data['block_expire']  = Arag_Config::get("block_expire");
        $data['block_counter'] = Arag_Config::get("block_counter");
        $data['saved']         = $this->session->get_once('user_settings_user_blocking_saved');

        $this->layout->content = new View('backend/settings_user_blocking', $data);
    }
    // }}}
    // {{{ user_blocking_write
    public function user_blocking_write()
    {

        Arag_Config::set('block_expire', $this->input->post('block_expire'));
        Arag_Config::set('block_counter', $this->input->post('block_counter'));

        $this->session->set('user_settings_user_blocking_saved', true);

        $this->user_blocking_read();

    }
    // }}}
    // {{{ user_blocking_write_error
    public function user_blocking_write_error()
    {
        $this->user_blocking_read();
    }
    // }}}
}
?>
