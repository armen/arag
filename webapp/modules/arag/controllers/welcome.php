<?php

class Welcome_Controller extends Controller 
{
    // {{{ constructor
    function __construct()
    {
        parent::__construct();
    }
    // }}}
    // {{{ index
    public function index()
    {
        $view = $this->load->view('index');
        $view->render(True);
    }
    // }}}
}

?>
