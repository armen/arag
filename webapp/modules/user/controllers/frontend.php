<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// |         Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend_Controller extends Controller 
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();
       
        // Frontend decorator
        $this->load->decorator('frontend/decorator');

        // Default page title
        $this->decorator->page_title = 'User Management';
    }
    // }}}
    // {{{ login_read
    function login_read()
    {
        $this->load->vars(array('showstatus' => false));
        $this->load->view('frontend/login');
    }
    // }}}
    // {{{ login_write
    function login_write()
    {
        $this->load->model('Users', 'Users');

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $status = $this->Users->check($username, $password);

        if ($status === 1) {

            // Set the privilege_filter to False, This is too 
            // important to Arag_Auth! we will fetch privilege 
            // filters of current application there.
            $this->session->del('privilege_filters');

            $this->session->set('authenticated', True);
            $this->session->set($this->Users->getUser($username));

            // Redirect to front controller
            url::redirect();
        
        } else {
            // Shit, you missed!
            $this->load->vars(array('status'     => $status,
                                    'showstatus' => true));
            $this->load->view('frontend/login');
        }
    }
    // }}}
    // {{{ logout
    function logout()
    {
        // Good bye!
        $this->session->destroy();

        // Redirect to front controller
        url::redirect();
    }
    // }}}
}

?>
