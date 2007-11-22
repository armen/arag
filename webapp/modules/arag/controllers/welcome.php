<?php

class Welcome_Controller extends Controller 
{
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
