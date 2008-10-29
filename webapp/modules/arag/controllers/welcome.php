<?php

class Welcome_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        $session = Session::instance();

        $theme = Kohana::config('theme.default');
        if ($session->get('user.authenticated')) {
            $this->layout = 'themes/'.$theme.'/backend_layout';
        } else {
            $this->layout = 'themes/'.$theme.'/frontend_layout';
        }

        parent::__construct();

        // Load the empty tabbed block
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
    }
    // }}}
    // {{{ index
    public function index_any()
    {
        $this->layout->content = new View('index');

        // Set the appname
        $this->layout->content->appname = ucfirst(APPNAME);
    }
    // }}}
}

?>
