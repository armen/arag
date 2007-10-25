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

        // Load the models
        $this->load->model('ApplicationsModel');        
        $this->load->model('GroupsModel');        
        $this->load->model('UsersModel');        

        // Load URL helper
        $this->load->helper('url');

        // Default page title
        $this->load->vars(Array('page_title' => 'User Management'));

        // load Clobal Tabs
        $this->load->component('TabbedBlock', 'global_tabs'); 

        // Backend decorator
        $this->load->decorator('backend/decorator');

    }
    // }}}
}

?>
