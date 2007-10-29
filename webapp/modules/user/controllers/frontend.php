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
       
        // Frontend decorator
        $this->load->decorator('frontend/decorator');

        // Load the URL helper
        $this->load->helper('url');        

        // Default page title
        $this->load->vars(Array('page_title' => 'User Management'));
    }
    // }}}
    // {{{ login_read
    function login_read()
    {
        $this->load->view('frontend/login');
    }
    // }}}
    // {{{ login_write
    function login_write()
    {
        $this->load->model('UsersModel', 'Users');

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // If you attempt to login I'll distroy your session /;)
        // This is so important to Arag_Auth! we will fetch 
        // privilege filters of current application there.
        $this->session->sess_destroy();        
        
        if ($this->Users->check($username, $password)) {

            $user =& $this->Users->getUser($username);

            $this->session->set_userdata('authenticated', True);
            $this->session->set_userdata($user);

            // Redirect to front controller
            redirect();
        
        } else {
            // Shit, you missed!
            redirect('user/frontend/login');
        }
    }
    // }}}
    // {{{ logout
    function logout()
    {
        // Good bye!
        $this->session->sess_destroy();

        // Redirect to front controller
        redirect();
    }
    // }}}
}

?>
