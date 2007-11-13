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
    public function login_read()
    {
        $this->load->vars(array('error_message' => false));
        $this->load->view('frontend/login');
    }
    // }}}
    // {{{ login_write
    public function login_write()
    {
        $this->load->model('Users', 'Users');

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if ($this->Users->check($username, $password, $status)) {

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
            if ($status & Users_Model::USER_NOT_FOUND) {
                $error_message[] = _("Wrong Username or Password"); 
            }

            if ($status & Users_Model::USER_NOT_VERIFIED) {
                $error_message[] = _("You are not a verified user."); 
            }

            if ($status & Users_Model::USER_BLOCKED) {
                $error_message[] = _("This user name is blocked!!! Please contact site administrator for further information."); 
            }

            if (!isset($error_message)) {
                $error_message[] = _("Unknown error");
            }

            $error_message = implode("\n", $error_message);

            $this->load->vars(array('status'        => $status,
                                    'error_message' => $error_message));
            $this->load->view('frontend/login');
        }
    }
    // }}}
    // {{{ logout
    public function logout()
    {
        // Good bye!
        $this->session->destroy();

        // Redirect to front controller
        url::redirect();
    }
    // }}}
}

?>
