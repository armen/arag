<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// |         Sasan Rose <sasan.rose@gmail.com>                               |
// |         Roham Rafii Tehrani <roham.rafii@gmail.com>                     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend_Controller extends Controller
{
    // {{{ properties
    private $message;
    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = 'User Management';

        // Validation messages
        $this->validation->message('required', _("%s is required"));
        $this->validation->message('length', _("%s has not enough length"));
        $this->validation->message('email', _("Please enter a valid email address"));
        $this->validation->message('_check_user_name', _("This %s is reserved or not available"));
        $this->validation->message('matches', _("%s does not match."));
        $this->validation->message('alpha_dash', _("%s can contain only alpha-numeric characters, underscores or dashes"));
        $this->validation->message('alpha', _("%s can contain only alpha characters"));
        $this->validation->message('smtp_error', _("SMTP settings are not set"));
        $this->validation->message('valid', _("Please enter a valid %s"));

        $this->message = False;

    }
    // }}}
    // {{{ login
    // {{{ login_read
    public function login_read()
    {
        $this->layout->content                  = new View('frontend/login', array('error_message' => false));
        $this->layout->content->display_captcha = $this->_check_display_captcha();
    }
    // }}}
    // {{{ login_write
    public function login_write()
    {
        $users = new Users_Model;

        $username  = $this->input->post('username');
        $password  = $this->input->post('password');

        if ($users->check($username, $password, $status, Arag_Config::get('block_expire', 0.5) * 3600)) {

            // Set the privilege_filter to False, This is too
            // important to Arag_Auth! we will fetch privilege
            // filters of current application there.
            $this->session->delete('privilege_filters');
            $this->session->delete('user');

            // Try to fetch user for this application, if there was not, try to fetch user information only
            // and grant default group access to him/her
            $user = $users->getUser($username);

            if (!isset($user['username'])) {
                // Fetch default group and merge it with user information
                $userinfo = $users->getUser($username, False);
                $user     = array_merge($users->getAnonymousUser(APPNAME, True), $userinfo); // Fetch default group
            }

            $this->session->set(Array('user' => array_merge($user, Array('authenticated' => True))));
            $users->unBlockUser($username);

            // Redirect to front controller or Redirect URL
            if ($this->session->get('not_authorized_redirect_url')) {
                url::redirect($this->session->get_once('not_authorized_redirect_url'));
            } else {
                $redirect_url = Kohana::config('user.login_redirect', False, False);
                Arag_Auth::is_accessible($redirect_url) ? url::redirect($redirect_url) : url::redirect(url::site());
            }

        } else {

            $this->session->keep_flash('not_authorized_redirect_url');

            // Shit, you missed!

            if ($status === Users_Model::USER_NOT_FOUND) {

                $error_message[] = _("Wrong Username or Password.");

                if (Arag_Config::get('block_counter', 3) != 0) {
                    $error_message[] = sprintf(_("Attention!! You will be blocked if you miss more than %s times while login!"),
                                               Arag_Config::get('block_counter', 3));
                }
            }

            if ($status & Users_Model::USER_INCORRECT_PASS) {

                $error_message[] = _("Wrong Username or Password.");
                $block_info = $users->getBlockInfo($username);

                if ((arag_config::get('block_counter', 3) != 0 && $block_info->block_counter >= arag_config::get('block_counter', 3))) {

                    $users->blockUser($username);
                    $error_message[] = sprintf(_("Attention!! Your username got blocked for %s hours!"), Arag_Config::get('block_expire', 0.5));

                } else if (!$block_info->blocked) { // If user is not already blocked

                    $users->addUserBlockCounter($username);

                    if (Arag_Config::get('block_counter', 3) != 0) {
                        $error_message[] = sprintf(_("Attention!! You will be blocked if you miss more than %s times while login!"),
                                                   Arag_Config::get('block_counter', 3) - $block_info->block_counter);
                    }
                }
            }

            if ($status & Users_Model::USER_NOT_VERIFIED) {
                $error_message[] = _("You are not a verified user.");
            }

            if ($status & Users_Model::USER_BLOCKED) {

                $block_info = $users->getBlockInfo($username);

                if ($block_info->block_date > 0) {

                    $error           = _("This user name is blocked. Please contact site administrator for further information or wait ".
                                         "for %s hours and %s minutes");
                    $waiting_hours   = floor((((Arag_Config::get('block_expire', 0.5) * 3600) + $block_info->block_date) - time()) / 3600);
                    $waiting_mins    = floor(((((Arag_Config::get('block_expire', 0.5) * 3600) + $block_info->block_date) - time()) % 3600) / 60);
                    $error_message[] = sprintf($error, $waiting_hours, $waiting_mins);

                } else {

                    $error_message[] = _("This user name is blocked. Please contact site administrator for further information.");
                }
            }

            if ($status & Users_Model::USER_GROUP_EXPIRED) {
                $error_message[] = _("Your group is expired.");
            }

            if (!isset($error_message)) {
                $error_message[] = _("Unknown error");
            }

            $error_message                          = implode("\n", $error_message);
            $this->layout->content                  = new View('frontend/login', array('status' => $status, 'error_message' => $error_message));
            $this->layout->content->display_captcha = $this->_check_display_captcha();
        }
    }
    // }}}
    // {{{ login_validate_write
    public function login_validate_write()
    {
        $this->validation->name('username', _("Username"))->pre_filter('trim', 'username')
             ->add_rules('username', 'required', 'valid::alpha_dash');

        $this->validation->name('password', _("Password"))->pre_filter('trim', 'password')
             ->add_rules('password', 'required');

        if ($this->_check_display_captcha($this->input->post('username'))) {
            $this->validation->message('valid', _("Please enter a valid %s"));
            $this->validation->name('captcha', _("Captcha"))->add_rules('captcha', 'Captcha::valid', 'required');
        }

        return $this->validation->validate();
    }
    // }}}
    // {{{ login_write_error
    public function login_write_error()
    {
        $this->login_read();
    }
    // }}}
    // {{{ login_json_write
    public function login_json_write()
    {
        $users = new Users_Model;

        $username  = $this->input->post('username');
        $password  = $this->input->post('password');

        if ($users->check($username, $password, $status, Arag_Config::get('block_expire', 0.5) * 3600, False)) {

            // Set the privilege_filter to False, This is too
            // important to Arag_Auth! we will fetch privilege
            // filters of current application there.
            $this->session->delete('privilege_filters');
            $this->session->delete('user');

            // Try to fetch user for this application, if there was not, try to fetch user information only
            // and grant default group access to him/her
            $user = $users->getUser($username);

            if (!isset($user['username'])) {
                // Fetch default group and merge it with user information
                $userinfo = $users->getUser($username, False);
                $user     = array_merge($users->getAnonymousUser(APPNAME, True), $userinfo); // Fetch default group
            }

            $this->session->set(Array('user' => array_merge($user, Array('authenticated' => True))));
            $users->unBlockUser($username);

            header("HTTP/1.0 200 OK");

        } else {

            // Shit, you missed!

            if ($status === Users_Model::USER_NOT_FOUND) {

                $error_message[] = _("Wrong Username or Password.");

                if (Arag_Config::get('block_counter', 3) != 0) {
                    $error_message[] = sprintf(_("Attention!! You will be blocked if you miss more than %s times while login!"),
                                               Arag_Config::get('block_counter', 3));
                }
            }

            if ($status & Users_Model::USER_INCORRECT_PASS) {

                $error_message[] = _("Wrong Username or Password.");
                $block_info = $users->getBlockInfo($username);

                if ((arag_config::get('block_counter', 3) != 0 && $block_info->block_counter >= arag_config::get('block_counter', 3))) {

                    $users->blockUser($username);
                    $error_message[] = sprintf(_("Attention!! Your username got blocked for %s hours!"), Arag_Config::get('block_expire', 0.5));

                } else if (!$block_info->blocked) { // If user is not already blocked

                    $users->addUserBlockCounter($username);

                    if (Arag_Config::get('block_counter', 3) != 0) {
                        $error_message[] = sprintf(_("Attention!! You will be blocked if you miss more than %s times while login!"),
                                                   Arag_Config::get('block_counter', 3) - $block_info->block_counter);
                    }
                }
            }

            if ($status & Users_Model::USER_NOT_VERIFIED) {
                $error_message[] = _("You are not a verified user.");
            }

            if ($status & Users_Model::USER_BLOCKED) {

                $block_info = $users->getBlockInfo($username);

                if ($block_info->block_date > 0) {

                    $error           = _("This user name is blocked. Please contact site administrator for further information or wait ".
                                         "for %s hours and %s minutes");
                    $waiting_hours   = floor((((Arag_Config::get('block_expire', 0.5) * 3600) + $block_info->block_date) - time()) / 3600);
                    $waiting_mins    = floor(((((Arag_Config::get('block_expire', 0.5) * 3600) + $block_info->block_date) - time()) % 3600) / 60);
                    $error_message[] = sprintf($error, $waiting_hours, $waiting_mins);

                } else {

                    $error_message[] = _("This user name is blocked. Please contact site administrator for further information.");
                }
            }

            if ($status & Users_Model::USER_GROUP_EXPIRED) {
                $error_message[] = _("Your group is expired.");
            }

            if (!isset($error_message)) {
                $error_message[] = _("Unknown error");
            }

            $error_message = implode("\n", $error_message);

            header("HTTP/1.0 401 Unauthorized");

            $this->layout->content = array('error' => $error_message);

        }

    }
    // }}}
    // }}}
    // {{{ logout_any
    public function logout_any()
    {
        // Good bye!
        $this->session->destroy();

        // Redirect to front controller
        url::redirect(Kohana::config('user.logout_redirect', False, False));
    }
    // }}}
    // {{{ forget_password
    // {{{ forget_password_read
    public function forget_password_read()
    {
        $this->layout->content = new View('frontend/password', array('message' => $this->message));
    }
    // }}}
    // {{{ forget_password_write
    public function forget_password_write()
    {
        $users     = new Users_Model;
        $multisite = Model::load('MultiSite', 'multisite');

        $is_sent = false;

        $email    = $this->input->post('email', Null, true);
        $username = $this->input->post('username', Null, true);
        $user     = $users->checkEmail($email, $username);

        if ($user['status'] === Users_Model::USER_NOT_FOUND) {
            $error_message = _("The entered information is not available in database. Please enter the correct username and email ".
                               "address or contact the site administrator.");

        } else {
            $error_message = false;
            $verify_uri    = $multisite->generateVerifyUri(10);

            // Send an email to verify the user
            $settings = Arag_Config::get('email_settings', NULL, 'core', False, Kohana::config('arag.master_appname'));
            $settings['template'] = _("You asked to change your password, for '%username%'. So please follow the below link:\n\n%verifyuri%\n\n".
                                      "And if you didn't ask to do so, please strongly visit:\n\n%removeuri%");
            $settings['subject']  = "Change Password";

            $strings  = array (
                               'verifyuri' => html::anchor('user/frontend/change_password/' . $verify_uri),
                               'username'  => $user['username'],
                               'removeuri' => html::anchor('user/frontend/remove/' . $verify_uri)
                              );

            $users->changePassword($user['username'], $verify_uri);

            try {
                $is_sent = $multisite->sendEmail($email, $strings, $settings);
            } catch(Swift_Exception $e) {
                // Shit, there was an error here!
                $is_sent = False;
                Kohana::log('error', $e->getMessage());
            }
        }

        $this->layout->content = new View('frontend/password_sent', array(
                                                                           'error_message' => $error_message,
                                                                           'is_sent'       => $is_sent,
                                                                           'message'       => $this->message
                                                                          ));
    }
    // }}}
    // {{{ forget_password_validate_write
    public function forget_password_validate_write()
    {
        $settings  = Arag_Config::get('email_settings', False, 'core', False, Kohana::config('arag.master_appname'));

        if (!$settings || !isset($settings['smtpserver']) || !isset($settings['sender']) || !isset($settings['smtpport'])) {
            $this->validation->name('smtp_settings', _("SMTP Settings"));
            $this->message = _("SMTP settings are not set");
            return false;
        }

        $this->validation->name('username', _("Username"))->pre_filter('trim', 'username')
              ->add_rules('username', 'required', 'valid::alpha_dash');

        $this->validation->name('email', _("Email"))->pre_filter('trim', 'email')
             ->add_rules('email', 'required', 'valid::email');

        $this->validation->name('captcha', _("Image's Text"))->add_rules('captcha', 'Captcha::valid', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ forget_password_write_error
    public function forget_password_write_error()
    {
        $this->forget_password_read();
    }
    // }}}
    // }}}
    // {{{ change_password
    // {{{ change_password_read
    public function change_password_read($verify_uri = false)
    {
        $users = new Users_Model;

        $error_message = false;
        $show_form     = true;

        if (!$verify_uri || !$users->hasUri($verify_uri, 1)) {
            $verify_uri    = false;
            $error_message = _("Please enter a valid uri to change your password");
            $show_form     = false;

        } else if (Arag_Config::get('expire', 0) != 0) {
            if ((time() - $users->expireDate($verify_uri)) > (Arag_Config::get('expire', 0) * 3600)) {
                $error_message = _("This uri is expired! Please contact site administrator for further information or make a new request.");
                $show_form     = false;
            }
        }

        $this->layout->content = new View('frontend/changepassword', array(
                                                                           'error_message' => $error_message,
                                                                           'is_sent'       => false,
                                                                           'verify_uri'    => $verify_uri,
                                                                           'show_form'     => $show_form,
                                                                           'message'       => $this->message
                                                                          ));
    }
    // }}}
    // {{{ change_password_write
    public function change_password_write()
    {
        $users     = new Users_Model;
        $multisite = Model::load('MultiSite', 'multisite');

        $is_sent    = false;
        $email      = $this->input->post('email', Null, true);
        $username   = $this->input->post('username', Null, true);
        $verify_uri = $this->input->post('verify_uri', Null, true);
        $user       = $users->checkEmail($email, $username, $verify_uri);

        if ($user['status'] === Users_Model::USER_NOT_FOUND) {
            $error_message = _("The entered information is not available in database. Please enter the correct username and email address or ".
                               "contact the site administrator.");

        } else {
            $error_message = false;
            $password   = strtolower(text::random('alnum', Arag_Config::get('passlength', 8)));

            // Send an email to verify the user
            $settings = Arag_Config::get('email_settings', NULL, 'core', False, Kohana::config('arag.master_appname'));
            $settings['template'] = _("Your new password for %username%' is '%password%'");
            $settings['subject']  = "Password Changed";
            $strings  = array (
                               'username' => $user['username'],
                               'password' => $password
                              );

            $users->changePassword($user['username'], '', $password);
            $users->changePassword($user['username'], NULL);

            try {
                $is_sent = $multisite->sendEmail($email, $strings, $settings);
            } catch(Swift_Exception $e) {
                // Shit, there was an error here!
                $is_sent = False;
                Kohana::log('error', $e->getMessage());
            }

        }

        $this->layout->content = new View('frontend/changepassword', array(
                                                                           'error_message' => $error_message,
                                                                           'is_sent'       => $is_sent,
                                                                           'verify_uri'    => $verify_uri,
                                                                           'show_form'     => false,
                                                                           'message'       => $this->message
                                                                          ));
    }
    // }}}
    // {{{ change_password_validate_write
    public function change_password_validate_write()
    {
        $settings  = Arag_Config::get('email_settings', False, 'core', False, Kohana::config('arag.master_appname'));

        if (!$settings || !isset($settings['smtpserver']) || !isset($settings['sender']) || !isset($settings['smtpport'])) {
            $this->validation->name('smtp_settings', _("SMTP Settings"));
            $this->message = _("SMTP settings are not set");
            return false;
        }

        $this->validation->name('username', _("Username"))->pre_filter('trim', 'username')
              ->add_rules('username', 'required', 'valid::alpha_dash');

        $this->validation->name('email', _("Email"))->pre_filter('trim', 'email')
             ->add_rules('email', 'required', 'valid::email');

        return $this->validation->validate();
    }
    // }}}
    // {{{ change_password_write_error
    public function change_password_write_error()
    {
        $this->change_password_read($this->input->post('verify_uri'));
    }
    // }}}
    // }}}
    // {{{ remove
    // {{{ remove_read
    public function remove_read($verify_uri)
    {
        $users = new Users_Model;

        $error_message = false;
        $show_form     = true;

        if (!$verify_uri || !$users->hasUri($verify_uri, 1)) {
            $verify_uri    = false;
            $error_message = _("Please enter a valid uri");
            $show_form     = false;

        } else if (Arag_Config::get('expire', 0) != 0) {
            if ((time()-$users->expireDate($verify_uri)) > (Arag_Config::get('expire', 0) * 3600)) {
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
        $users     = new Users_Model;
        $multisite = Model::load('MultiSite', 'multisite');

        $removed    = false;
        $show_form  = true;
        $email      = $this->input->post('email', Null, true);
        $username   = $this->input->post('username', Null, true);
        $verify_uri = $this->input->post('verify_uri', Null, true);
        $user       = $users->checkEmail($email, $username, $verify_uri);

        if ($user['status'] === Users_Model::USER_NOT_FOUND) {
            $error_message = _("The entered information is not available in database. Please enter the correct username and email address or ".
                               "contact the site administrator.");

        } else {
            $error_message = false;
            $show_form     = false;
            $removed       = true;

            $users->changePassword($user['username'], NULL);
        }

        $this->layout->content = new View('frontend/remove', array(
                                                                   'removed'       => $removed,
                                                                   'error_message' => $error_message,
                                                                   'verify_uri'    => $verify_uri,
                                                                   'show_form'     => $show_form
                                                                  ));
    }
    // }}}
    // {{{ remove_validate_write
    public function remove_validate_write()
    {
        $this->validation->name('username', _("Username"))->pre_filter('trim', 'username')
              ->add_rules('username', 'required');

        $this->validation->name('email', _("Email"))->pre_filter('trim', 'email')
             ->add_rules('email', 'required', 'valid::email');

        return $this->validation->validate();
    }
    // }}}
    // {{{ remove_write_error
    public function remove_write_error()
    {
        $this->remove_read($this->input->post('verify_uri'));
    }
    // }}}
    // }}}
    // {{{ registration
    // {{{ registration_read
    public function registration_read()
    {
        $this->layout->content = new View('frontend/user_registration', array(
                                                                              'flagsaved' => false,
                                                                              'message'   => $this->message
                                                                             ));
    }
    // }}}
    // {{{ registration_write
    public function registration_write()
    {
        $users     = new Users_Model;
        $groups    = new Groups_Model;
        $multisite = Model::load('MultiSite', 'multisite');

        $groupname  = $groups->getDefaultGroup(APPNAME);
        $email      = $this->input->post('email', Null, true);
        $name       = strtolower($this->input->post('name', Null, true));
        $lastname   = $this->input->post('lastname', Null, true);
        $username   = $this->input->post('username', Null, true);
        $password   = $this->input->post('password', Null, true);
        $verify_uri = $multisite->generateVerifyUri(10);

        $users->createUser(APPNAME, $email, $name, $lastname, $groupname, $username, $password, 'Anonymous', $verify_uri , 0);

        // Send an email to verify the user
        $settings = Arag_Config::get('email_settings', NULL, 'core', False, Kohana::config('arag.master_appname'));
        $settings['template'] = _("Thank you for registering in %appname%. To complete you registration please visit the following link".
                                  ":\n\n%verifyuri%\n\nYour Username: %username%\nYour Password: %password%");
        $settings['subject']  = "Registration";
        $strings  = array (
                           'verifyuri' => html::anchor('user/frontend/verify/' . $verify_uri),
                           'username'  => $username,
                           'appname'   => APPNAME,
                           'password'  => $password
                          );

        try {

            $is_sent = $multisite->sendEmail($email, $strings, $settings);

        } catch(Swift_Exception $e) {

            // Shit, there was an error here!
            $is_sent = False;
            Kohana::log('error', $e->getMessage());
        }

        $this->layout->content = new View('frontend/user_registration', array(
                                                                              'flagsaved' => true,
                                                                              'is_sent'   => $is_sent,
                                                                              'message'   => $this->message
                                                                             ));
    }
    // }}}
    // {{{ registration_validate_write
    public function registration_validate_write()
    {
        $settings  = Arag_Config::get('email_settings', False, 'core', False, Kohana::config('arag.master_appname'));

        if (!$settings || !isset($settings['smtpserver']) || !isset($settings['sender']) || !isset($settings['smtpport'])) {
            $this->validation->name('smtp_settings', _("SMTP Settings"));
            $this->message = _("SMTP settings are not set");
            return false;
        }

        $this->validation->name('username', _("Username"))->pre_filter('trim', 'username')
              ->add_rules('username', 'required', 'length[4, 255]', 'valid::alpha_dash', array($this, '_check_user_name'));

        $this->validation->name('password', _("Password"))->pre_filter('trim', 'password')
             ->add_rules('password', 'required', 'matches[repassword]');

        $this->validation->name('repassword', _("Repassword"))->pre_filter('trim', 'repassword')
             ->add_rules('repassword', 'required');

        $this->validation->name('name', _("Name"))->pre_filter('trim', 'name')
             ->add_rules('name', 'required', 'valid::standard_text');

        $this->validation->name('lastname', _("Lastname"))->pre_filter('trim', 'lastname')
             ->add_rules('lastname', 'required', 'valid::standard_text');

        $this->validation->name('email', _("Email"))->pre_filter('trim', 'email')
             ->add_rules('email', 'required', 'valid::email', 'matches[reemail]');

        $this->validation->name('reemail', _("Email"))->pre_filter('trim', 'reemail')
             ->add_rules('reemail', 'required', 'valid::email');

        $this->validation->name('captcha', _("Image's Text"))->add_rules('captcha', 'Captcha::valid', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ registration_write_error
    public function registration_write_error()
    {
        $this->registration_read();
    }
    // }}}
    // }}}
    // {{{ verify
    // {{{ verify_read
    public function verify_read($verify_uri = false)
    {
        $users = new Users_Model;

        $show_form     = true;
        $error_message = false;

        if (!$verify_uri || !$users->hasUri($verify_uri)) {
            $error_message = _("Please enter a valid uri!");
            $show_form     = false;

        } else if (Arag_Config::get('expire', 0, 'user') != 0) {
            if ((time()-$users->expireDate($verify_uri)) > (Arag_Config::get('expire', 0, 'user') * 3600)) {
                $error_message = _("This uri is expired! Please contact site administrator for further information.");
                $show_form     = false;
            }
        }

        $data = array('error_message' => $error_message,
                      'show_form'     => $show_form,
                      'uri'           => $verify_uri);
        $this->layout->content = new View('frontend/verify', $data);
    }
    // }}}
    // {{{ verify_write
    public function verify_write()
    {
        $users = new Users_Model;

        $show_form  = true;
        $verify_uri = $this->input->post('uri');
        $username   = $this->input->post('username');
        $password   = $this->input->post('password');

        if ($users->checkVerify($username, $password, $verify_uri, $status, Arag_Config::get('block_expire', 0.5) * 3600)) {
            $users->verify($username, $password, $verify_uri);
            $users->unBlockUser($username);
            $this->layout->content = new View('frontend/verified');

        } else {

            // Shit, you missed!
            if ($status === Users_Model::USER_NOT_FOUND || Users_Model::USER_INCORRECT_PASS) {
                $error_message[] = _("Wrong Username or Password.");
            }

            if ($status & Users_Model::USER_BLOCKED) {
                $block_info      = $users->getBlockInfo($username);
                if ($block_info->block_date > 0) {
                    $error           = _("This user name is blocked. Please contact site administrator for further information or wait for ".
                                         "%h% hours and %m% minutes");
                    $waiting_hours   = floor((((Arag_Config::get('block_expire', 0.5) * 3600) + $block_info->block_date) - time()) / 3600);
                    $waiting_mins    = floor(((((Arag_Config::get('block_expire', 0.5) * 3600) + $block_info->block_date) - time()) % 3600) / 60);
                    $error_message[] = str_replace(array('%h%', '%m%'), array($waiting_hours, $waiting_mins) , $error);

                } else {
                    $error_message[] = _("This user name is blocked. Please contact site administrator for further information.");

                }
            }

            $error_message = implode("\n", $error_message);

            $data = array('error_message' => $error_message,
                          'show_form'     => $show_form,
                          'uri'           => $verify_uri);

            $this->layout->content = new View('frontend/verify', $data);
        }
    }
    // }}}
    // }}}
    // {{{ _check_user_name
    public function _check_user_name($username)
    {
        $users = new Users_Model;

        return (!preg_match("/^[a-z0-9_.]+_admin$/", strtolower($username)) &&
                !$users->isRegistered($username) && preg_match("/^[a-z][a-z0-9_.]*$/", strtolower($username)));
    }
    // }}}
    // {{{ _check_display_captcha
    public function _check_display_captcha($username = Null)
    {
        if ($username === Null) {
            $username = $this->session->get('captcha_username');
        } else {
            $this->session->set('captcha_username', $username);
        }

        $users = new Users_Model;
        $user  = $users->getBlockInfo($username);

        return ($user && (Arag_Config::get('captcha_counter', 3) != 0 && $user->block_counter >= Arag_Config::get('captcha_counter', 3) + 1));
    }
    // }}}
     // {{{ search_write
    public function search_write()
    {
        $usersMan = New users_Model;
        $simple   = Array();
        $results  = $usersMan->getUsers(Null, Null, Null, $this->input->post('username'));
        foreach($results as $result) {
            $simple[] = $result['username'];
        }
        print json_encode($simple);
        die();
    }
    // }}}
}
