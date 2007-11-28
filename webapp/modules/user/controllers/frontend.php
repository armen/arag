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
       
        // Default page title
        $this->layout->page_title = 'User Management';
    }
    // }}}
    // {{{ login_read
    public function login_read()
    {
        $this->layout->content = new View('frontend/login', array('error_message' => false));
    }
    // }}}
    // {{{ login_write
    public function login_write()
    {
        $this->load->model('Users', 'Users');

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if ($this->Users->check($username, $password, $status, Arag_Config::get('block_expire', 0.5) * 3600)) {

            // Set the privilege_filter to False, This is too 
            // important to Arag_Auth! we will fetch privilege 
            // filters of current application there.
            $this->session->del('privilege_filters');

            $this->session->set('authenticated', True);
            $this->session->set($this->Users->getUser($username));

            $this->Users->blockUser($username);

            // Redirect to front controller
            url::redirect(Config::item('user.login_redirect', False, False));
        
        } else {

            // Shit, you missed!
            if ($status === Users_Model::USER_NOT_FOUND) {
                $error_message[] = _("Wrong Username or Password.");
                if (Arag_Config::get('block_counter', 3) != 0) {
                    $error = _("Attention!! You will be blocked if you miss more than %s% times while login!");
                    $error_message[] = str_replace('%s%', Arag_Config::get('block_counter', 3), $error);
                }
            }

            if ($status & Users_Model::USER_INCORRECT_PASS) {
                $error_message[] = _("Wrong Username or Password."); 
                $block_info = $this->Users->getBlockInfo($username);

                if (Arag_Config::get('block_counter', 3) != 0 && $block_info->block_counter >= Arag_Config::get('block_counter', 3)) {
                    $this->Users->blockUser($username, 1, 0, time());

                    $error           = _("Attention!! Your username got blocked for %s% hours!");
                    $error_message[] = str_replace('%s%', Arag_Config::get('block_expire', 0.5), $error);

                } else {
                    $this->Users->blockUser($username, 0, ++$block_info->block_counter);
                    if (Arag_Config::get('block_counter', 3) != 0) {
                        $error = _("Attention!! You will be blocked if you miss more than %s% times while login!");
                        $error_message[] = str_replace('%s%', Arag_Config::get('block_counter', 3), $error);
                    }

                }
            }

            if ($status & Users_Model::USER_NOT_VERIFIED) {
                $error_message[] = _("You are not a verified user."); 
            }

            if ($status & Users_Model::USER_BLOCKED) {
                $block_info      = $this->Users->getBlockInfo($username);
                if ($block_info->block_date > 0) {
                    $error           = _("This user name is blocked. Please contact site administrator for further information or wait for %h% hours and %m% minutes");
                    $waiting_hours   = floor((((Arag_Config::get('block_expire', 0.5) * 3600) + $block_info->block_date) - time()) / 3600);
                    $waiting_mins    = floor(((((Arag_Config::get('block_expire', 0.5) * 3600) + $block_info->block_date) - time()) % 3600) / 60);
                    $error_message[] = str_replace(array('%h%', '%m%'), array($waiting_hours, $waiting_mins) , $error);                   

                } else {
                    $error_message[] = _("This user name is blocked. Please contact site administrator for further information."); 

                }
            }

            if (!isset($error_message)) {
                $error_message[] = _("Unknown error");
            }

            $error_message = implode("\n", $error_message);

            $this->layout->content = new View('frontend/login', array('status' => $status, 'error_message' => $error_message));
        }
    }
    // }}}
    // {{{ login_write_error
    public function login_write_error()
    {
        $this->login_read();
    }
    // }}}
    // {{{ logout
    public function logout()
    {
        // Good bye!
        $this->session->destroy();

        // Redirect to front controller
        url::redirect(Config::item('user.logout_redirect', False, False));
    }
    // }}}
    // {{{ forget_password_read
    public function forget_password_read()
    {
        $this->layout->content = new View('frontend/password', array(
                                                                     'error_message' => false,
                                                                     'is_sent'       => false,
                                                                    ));
    }
    // }}}
    // {{{ forget_password_write
    public function forget_password_write()
    {
        $this->load->model('Users', 'Users'); 
        $this->load->model('MultiSite', NULL, 'multisite');

        $is_sent = false;

        $email    = $this->input->post('email', true);
        $username = $this->input->post('username', true);
        $user     = $this->Users->checkEmail($email, $username);

        if ($user['status'] === Users_Model::USER_NOT_FOUND) {
            $error_message = _("The entered information is not available in database. Please enter the correct username and email address or contact the site administrator.");

        } else {
            $error_message = false;
            $verify_uri = $this->MultiSite->generateVerifyUri(10); 

            // Send an email to verify the user
            $settings = Arag_Config::get('email_settings', NULL, 'core');
            $settings['template'] = "You asked to change your password, for '%username%'. So please follow the below link:\n\n%verifyuri%\n\nAnd if you didn't ask to do so, please strongly visit:\n\n%removeuri%";
            $settings['subject']  = "Change Password";
            $strings  = array (
                               'verifyuri' => html::anchor('user/frontend/change_password/' . $verify_uri),
                               'username'  => $user['username'],
                               'removeuri' => html::anchor('user/frontend/remove/' . $verify_uri)
                              );

            $this->Users->changePassword($user['username'], $verify_uri);

            try {
                $is_sent = $this->MultiSite->sendEmail($email, $strings, $settings);
            } catch(Swift_Exception $e) {
                // Shit, there was an error here!
                $is_sent = False;
            }

        }

        $this->layout->content = new View('frontend/password', array(
                                                                     'error_message' => $error_message,
                                                                     'is_sent'       => $is_sent
                                                                    ));
    }
    // }}}
    // {{{ forget_password_write_error
    public function forget_password_write_error()
    {
        $this->forget_password_read();
    }
    // }}}
    // {{{ change_password_read
    public function change_password_read($verify_uri = false)
    {
        $error_message = false;
        $show_form     = true;
        $this->load->model('Users', 'Users'); 

        if (!$verify_uri || !$this->Users->hasUri($verify_uri, 1)) {
            $verify_uri    = false;
            $error_message = _("Please enter a valid uri to change your password");
            $show_form     = false;

        } else if (Arag_Config::get('expire', 0) != 0) {
            if ((time()-$this->Users->expireDate($verify_uri)) > (Arag_Config::get('expire', 0) * 3600)) {
                $error_message = _("This uri is expired! Please contact site administrator for further information or make a new request.");
                $show_form     = false;            
            }
        }

        $this->layout->content = new View('frontend/changepassword', array(
                                                                           'error_message' => $error_message,
                                                                           'is_sent'       => false,
                                                                           'verify_uri'    => $verify_uri,
                                                                           'show_form'     => $show_form
                                                                          ));
    }
    // }}}
    // {{{ change_password_write
    public function change_password_write()
    {
        $this->load->model('Users', 'Users'); 
        $this->load->model('MultiSite', NULL, 'multisite');

        $is_sent   = false;

        $email      = $this->input->post('email', true);
        $username   = $this->input->post('username', true);
        $verify_uri = $this->input->post('verify_uri', true);
        $user       = $this->Users->checkEmail($email, $username, $verify_uri);

        if ($user['status'] === Users_Model::USER_NOT_FOUND) {
            $error_message = _("The entered information is not available in database. Please enter the correct username and email address or contact the site administrator.");

        } else {
            $error_message = false;
            $password   = strtolower(text::random('alnum', Arag_Config::get('passlength', 8)));

            // Send an email to verify the user
            $settings = Arag_Config::get('email_settings', NULL, 'core');
            $settings['template'] = "Your new password for %username%' is '%password%'";
            $settings['subject']  = "Password Changed";
            $strings  = array (
                               'username' => $user['username'],
                               'password' => $password
                              );

            $this->Users->changePassword($user['username'], '', $password);
            $this->Users->changePassword($user['username'], NULL);

            try {
                $is_sent = $this->MultiSite->sendEmail($email, $strings, $settings);
            } catch(Swift_Exception $e) {
                // Shit, there was an error here!
                $is_sent = False;
            }

        }

        $this->layout->content = new View('frontend/changepassword', array(
                                                                           'error_message' => $error_message,
                                                                           'is_sent'       => $is_sent,
                                                                           'verify_uri'    => $verify_uri,
                                                                           'show_form'     => false
                                                                          ));
    }
    // }}}
    // {{{ change_password_write_error
    public function change_password_write_error()
    {
        $this->change_password_read($this->input->post('verify_uri'));
    }
    // }}}
    // {{{ remove_read
    public function remove_read($verify_uri)
    {
        $error_message = false;
        $show_form     = true;
        $this->load->model('Users', 'Users'); 

        if (!$verify_uri || !$this->Users->hasUri($verify_uri, 1)) {
            $verify_uri    = false;
            $error_message = _("Please enter a valid uri");
            $show_form     = false;

        } else if (Arag_Config::get('expire', 0) != 0) {
            if ((time()-$this->Users->expireDate($verify_uri)) > (Arag_Config::get('expire', 0) * 3600)) {
                $error_message = _("This uri is expired! Please contact site administrator for further information or make a new request.");
                $show_form     = false;            
            }
        }

        $this->layout->content = new View('frontend/remove', array(
                                                                   'removed'       => false,
                                                                   'error_message' => $error_message,
                                                                   'verify_uri'    => $verify_uri,
                                                                   'show_form'     => $show_form
                                                                  ));
    }
    // }}}
    // {{{ remove_write
    public function remove_write()
    {
        $this->load->model('Users', 'Users'); 
        $this->load->model('MultiSite', NULL, 'multisite');

        $removed   = false;
        $show_form = true;

        $email      = $this->input->post('email', true);
        $username   = $this->input->post('username', true);
        $verify_uri = $this->input->post('verify_uri', true);
        $user       = $this->Users->checkEmail($email, $username, $verify_uri);

        if ($user['status'] === Users_Model::USER_NOT_FOUND) {
            $error_message = _("The entered information is not available in database. Please enter the correct username and email address or contact the site administrator.");

        } else {
            $error_message = false;
            $show_form     = false;
            $removed       = true;

            $this->Users->changePassword($user['username'], NULL);
        }

        $this->layout->content = new View('frontend/remove', array(
                                                                   'removed'       => $removed,
                                                                   'error_message' => $error_message,
                                                                   'verify_uri'    => $verify_uri,
                                                                   'show_form'     => $show_form
                                                                  ));
    }
    // }}}
    // {{{ remove_write_error
    public function remove_write_error()
    {
        $this->remove_read($this->input->post('verify_uri'));
    }
    // }}}
}

?>
