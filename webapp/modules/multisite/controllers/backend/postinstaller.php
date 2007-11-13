<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// |         Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class PostInstaller_Controller extends Backend_Controller 
{
    // {{{ index_read
    public function index_read()
    {   
        $this->load->vars(array(
                                'email_is_sent' => $this->session->get_once('multisite_email_is_sent'),
                                'installed'     => $this->session->get_once('multisite_installed')
                               ));

        $this->load->view('backend/post_installer');
    }
    // }}}
    // {{{ index_write
    public function index_write()
    {
    }
    // }}}
}

?>
