<?php

class Welcome_Controller extends Controller 
{
    // {{{ Constructor
    public function __construct()
    {
        $session = Session::instance();

        if ($session->get('user.authenticated')) {
            $this->layout = 'arag_templates/backend_layout';
        } else {
            $this->layout = 'arag_templates/frontend_layout';
        }

        parent::__construct();

        // Load the empty tabbed block
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
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
