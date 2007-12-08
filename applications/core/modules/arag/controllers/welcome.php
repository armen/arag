<?php

class Welcome_Controller extends Controller 
{
    // {{{ Constructor
    public function __construct()
    {
        $session = new Session;

        if ($session->get('authenticated')) {
            $this->layout = 'arag_templates/backend_layout';
        } else {
            $this->layout = 'arag_templates/frontend_layout';
        }

        parent::__construct();

        // Load the empty tabbed block
        $this->load->component('TabbedBlock', 'global_tabs');        
    }
    // }}}
    // {{{ index
    public function index()
    {
        $this->layout->content = new View('index');      

        // Set the appname
        $this->layout->content->appname = ucfirst(APPNAME);
    }
    // }}}
}

?>
