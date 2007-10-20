<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend extends Arag_Controller 
{
    // {{{ Constructor
    function Backend()
    {
        parent::Arag_Controller();

        // Load the model
        $this->load->model('UserManagementModel');        
       
        // Backend decorator
        $this->load->decorator('backend/decorator');

        // Default page title
        $this->load->vars(Array('page_title' => 'User Management'));

        // Global tabbedbock
        $this->load->component('TabbedBlock', 'global_tabs');
        $this->global_tabs->setTitle(_("User Management"));
        $this->global_tabs->addItem(_("Apllications"), 'user/backend/applications');
        $this->global_tabs->addItem(_("Groups"), 'user/backend/groups');
        $this->global_tabs->addItem(_("Users"), 'user/backend/users');
        $this->global_tabs->addItem(_("Settings"), 'user/backend/settings');
    }
    // }}}
}

?>
