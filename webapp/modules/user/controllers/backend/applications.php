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
    }
    // }}}
    // {{{ index
    public function index($page = Null)
    {
        $this->load->component('PList', 'applications');

        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {        
            $name = $this->session->get('user_app_name');
        } else {
            $name = $this->input->post('name', True);
        }

        $this->session->set('user_app_name', $name);

        $this->applications->setResource($this->Applications->getApps($name));
        $this->applications->setLimit(Arag_Config::get('limit', 0));
        $this->applications->addColumn('name', _("Name"));        
        $this->applications->addColumn('default_group', _("Default Group"));
        $this->applications->addColumn('Applications.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->applications->addAction('user/backend/applications/groups/#name#', _("Edit"), 'edit_action');      
        
        $this->load->vars(array("name" => $name,
                                "flag" => false));
        $this->load->view('backend/index'); 
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
        $this->groups->addAction("user/backend/applications/delete/group", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->groups->setGroupActionParameterName('id');

        $this->session->set('delete_appname', $appname);

        $this->load->view('backend/groups'); 
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
        $this->users->addAction("user/backend/applications/delete/user", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username');
        
        $this->load->vars(array("flagsearch"  => false));        

        $this->load->view('backend/users');
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
        $this->users->addAction("user/backend/applications/delete/user", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->users->setGroupActionParameterName('username');
        
        $this->load->vars(array("flagsearch" => true,
                                "user"       => $user,
                                "app_name"   => $app_name,
                                "group_name" => $group_name,
                                "flagform"   => true));        

        $this->load->view('backend/users');    
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
        
        $this->load->vars(array('objects'  => $objects,
                                'subjects' => $subjects,
                                'flag'     => $flag,
                                'appname'  => $appname,
                                'flagform' => true));

        $this->load->view('backend/delete');
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
                $this->Groups->deleteGroups($objects, $this->session->get('username'));
                url::redirect('user/backend/applications/groups/'.$application);
            } else {
                $this->Users->deleteUsers($objects, NULL, $this->session->get('username'));
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
        $this->load->component('PList', 'applications');

        if ($page != Null && preg_match('|page[a-z_]*:[0-9]*|', $page)) {        
            $name = $this->session->get('user_app_name');
        } else {
            $name = $this->input->post('name', True);
        }

        $this->session->set('user_app_name', $name);

        $this->applications->setResource($this->Filters->getFilterProperties($name, false));
        $this->applications->setLimit(Arag_Config::get('limit', 0));
        $this->applications->addColumn('appname', _("Name"));        
        $this->applications->addColumn('created_by', _("Created By"));
        $this->applications->addColumn('modified_by', _("Modified By"));
        $this->applications->addColumn('Filters.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->applications->addColumn('Filters.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);      
        $this->applications->addAction('user/backend/applications/app_filters/#appname#', _("Edit"), 'edit_action');
        $this->applications->addAction('user/backend/applications/app_filters_delete/#appname#', _("Delete"), 'delete_action', 'Filters.isDeletable');
        $this->applications->addAction("user/backend/applications/app_filters_delete/", _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->applications->setGroupActionParameterName('appname');
        
        $this->load->vars(array("name" => $name,
                                "flag" => true));
        $this->load->view('backend/index');
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

        $this->Filters->addFilter($filter, $appname, $this->session->get('username'));
        
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

        $this->load->component('PList', 'filters_pro');

        $this->filters_pro->setResource($this->Filters->getFilterProperties($appname));
        $this->filters_pro->setLimit(Arag_Config::get('limit', 0));
        $this->filters_pro->addColumn('appname', _("Name"));        
        $this->filters_pro->addColumn('created_by', _("Created By"));
        $this->filters_pro->addColumn('modified_by', _("Modified By"));
        $this->filters_pro->addColumn('Filters.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->filters_pro->addColumn('Filters.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);

        $this->load->component('PList', 'filters');

        $this->filters->setResource($this->Filters->getFilters($appname));
        $this->filters->setLimit(Arag_Config::get('limit', 0));
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

        $this->load->vars(array('appname'   => $appname,
                                'id'        => $id,
                                'flagsaved' => $flagsaved,
                                'filter'    => $filters[$id]['filter']));

        $this->load->view('backend/edit_filters');
    }
    //}}}
    //{{{ filters_edit_write
    public function filters_edit_write()
    {
        $filter  = $this->input->post('filter');
        $appname = $this->input->post('application');
        $id      = $this->input->post('id');

        $this->Filters->editFilter($filter, $id, $appname, $this->session->get('username'));

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
        
        $this->load->vars(array('objects'    => $objects,
                                'subjects'   => $subjects,
                                'objecttype' => $objecttype,
                                'appname'    => $appname));

        $this->load->view('backend/filters_delete');
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
        
        $this->load->vars(array('objects'    => $objects,
                                'subjects'   => $subjects,
                                'objecttype' => $objecttype,
                                'appname'    => $key));

        $this->load->view('backend/filters_delete');
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
        $this->load->vars(array('flagsaved' => $this->session->get_once('app_add_filter_saved')));

        $this->load->view('backend/add_app_filter');

    }
    // }}}
    // {{{ add_apps_filters_write
    public function add_apps_filters_write()
    {
        $appname = $this->input->post('appname');
        $this->Filters->addApp($appname, $this->session->get('username'));

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
      
        $this->Privileges->addLabel($label, 0, NULL, $this->session->get('username'));

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

        $this->Privileges->addLabel($label, $parentid, $privilege, $this->session->get('username'));

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

        $this->load->vars(array('label'     => $label->label,
                                'privilege' => $label->privilege,
                                'id'        => $id,
                                'parentid'  => $label->parent_id,
                                'flagsaved' => $this->session->get_once('privilege_edited_saved')));

        $this->load->view('backend/privileges_edit');
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

        $this->Privileges->editLabel($label, $id, $privilege, $this->session->get('username'));

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
        
        $this->load->vars(array('objects'     => $objects,
                                'subjects'    => $subjects,
                                'flagcaption' => $flagcaption));

        $this->load->view('backend/privileges_delete');
    }
    // }}}
    // {{{  privileges_do_delete
    public function privileges_do_delete()
    {
        $objects = $this->input->post('objects');
        
        $this->Privileges->deletePrivileges($objects, $this->session->get('username'));

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
        $this->_user_profile_write($this->input->post('appname'));       
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
}
?>
