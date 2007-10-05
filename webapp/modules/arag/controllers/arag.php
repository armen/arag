<?php

class Arag extends Arag_Controller 
{
    // {{{ constructor
    function Arag()
    {
        parent::Arag_Controller();
    }
    // }}}
    // {{{ index
    function index()
    {
        $this->load->view('index');
    }
    // }}}
}

?>
