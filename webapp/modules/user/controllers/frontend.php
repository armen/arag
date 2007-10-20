<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend extends Arag_Controller 
{
    // {{{ Constructor
    function Frontend()
    {
        parent::Arag_Controller();
       
        // Backend decorator
        $this->load->decorator('frontend/frontend_decorator');

        // Default page title
        $this->load->vars(Array('page_title' => 'TA Locator'));
    }
    // }}}
    // {{{ index
    function index()
    {
        
    }
    // }}}
}

?>
