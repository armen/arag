<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ Properties

    protected $username = Null;
    protected $section  = 'backend';

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Load the models
        $this->UserProfile = new UserProfile_Model;
        $this->Users       = Model::load('Users', 'user');

        // load global Tabs
        $this->globals_tabs = new TabbedBlock_Component('global_tabs');

        // Default page title
        $this->layout->page_title = 'User Profile';

        // Get the appname
        $this->username = $this->session->get('user.username');

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("User Profile"));
        $this->global_tabs->addItem(_("Personal Information"), 'user_profile/'.$this->section.'/index');
        $this->global_tabs->addItem(_("Change Password"), 'user_profile/'.$this->section.'/password');

        // Validation Messages
        $passwordLength = Arag_Config::get("passlength", 0, 'user');
        $this->validation->message('required', _("%s is required"));
        $this->validation->message('matches', _("%ss do not match"));
        $this->validation->message('numeric', _("%s should be numeric"));
        $this->validation->message('postal_code_length', _("%s should be between 5 to 10 digits"));
        $this->validation->message('phone_length', _("%s should be 8 digits or shorter."));
        $this->validation->message('cellphone_length', _("%s should be exactly 11 digits"));
        $this->validation->message('_check_old_password', _("Please enter correct %s"));
        $this->validation->message('oldpassword_length',sprintf(_("Password length should be at least %s characters "), $passwordLength));
        $this->validation->message('newpassword_length',sprintf(_("New Password length should be at least %s characters "), $passwordLength));
    }
    // }}}
    // {{{ index_read
    public function index_read($username = Null)
    {

        $is_admin = Arag_Auth::is_accessible('user_profile/backend');
        if ($username) {
            if (!$is_admin) {
                return False;
                $this->_invalid_request();
            }
        } else {
            $username = $this->username;
        }

        $data          = $this->Users->getUserProfile($username);
        $isset_profile = False;

        if ($isset_profile = $this->UserProfile->hasUserName($username)) {
            $data          = array_merge($data, $this->UserProfile->getProfile($username));
            $isset_profile = True;
        }

        $data = array_merge($data, array (
                                          'flagsaved'     => $this->session->get_once('user_profile_profile_saved'),
                                          'isset_profile' => $isset_profile,
                                          'username'      => $username,
                                          'section'       => $this->section,
                                          'is_admin'      => $is_admin
                                         ));

        $this->layout->content = new View($this->section.'_user_profile', $data);
    }
    // }}}
    // {{{ index_write
    public function index_write($username=Null)
    {   
        $is_admin = Arag_Auth::is_accessible('user_profile/backend');
        if ($username) {
            if (!$is_admin) {
                $this->_invalid_request();
                return False;
            }
        } else {
            $username = $this->username;
        }

        $data        = $this->Users->getUserProfile($username);
        $location    = locations::get($this->input->post('location', Null, true));
        $address     = $this->input->post('address', Null, true);
        $phone       = $this->input->post('phone', Null, true);
        $cellphone   = $this->input->post('cellphone', Null, true);
        $postal_code = $this->input->post('postal_code', Null, true);

        if ($this->UserProfile->hasUserName($username)) {
            $this->UserProfile->editProfile($address, $phone, $cellphone, $postal_code, $username, $location);
        } else {
            $this->UserProfile->insertProfile($address, $phone, $cellphone, $postal_code, $username, $data['name'], $data['lastname'], $location);
        }

        $this->session->set('user_profile_profile_saved', true);

        $this->index_read();
    }
    // }}}
    // {{{ index_validate_write
    public function index_validate_write()
    {
        $this->validation->name('phone', _("Phone"))->add_rules('phone', 'required', 'valid::numeric', 'length[0, 8]');
        $this->validation->name('cellphone', _("Cellphone"))->add_rules('cellphone', 'valid::numeric', 'length[11, 11]');
        $this->validation->name('location', _("Location"))->add_rules('location', 'required');
        $this->validation->name('postal_code', _("Postal Code"))->add_rules('postal_code', 'valid::numeric', 'length[5, 10]');

        return $this->validation->validate();
    }
    // }}}
    // {{{ index_write_error
    public function index_write_error($username=null)
    {
        $this->index_read($username);
    }
    // }}}
    // {{{ password_read
    public function password_read()
    {
        $this->layout->content = new View($this->section.'_change_password', array('flagsaved' => $this->session->get_once('user_profile_password_saved'),
                                                                                   'section'   => $this->section));
    }
    // }}}
    // {{{ password_write
    public function password_write()
    {
        $newpassword = $this->input->post('newpassword');
        $oldpassword = $this->input->post('oldpassword');

        $this->Users->changePassword($this->username, '', $newpassword);
        $this->session->set('user_profile_password_saved', true);

        url::redirect('user_profile/'.$this->section.'/password');
    }
    // }}}
    // {{{ password_validate_write
    public function password_validate_write()
    {
        $passwordLength = Arag_Config::get("passlength", 0, 'user');

        $this->validation->name('oldpassword', _("Old Password"))->add_rules('oldpassword', 'required', array($this, '_check_old_password'), 'length['.$passwordLength.', 255]');
        $this->validation->name('newpassword', _("Password"))->add_rules('newpassword', 'required', 'matches[renewpassword]', 'length['.$passwordLength.', 255]');
        $this->validation->name('renewpassword', _("Re-Password"))->add_rules('renewpassword', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ password_write_error
    public function password_write_error()
    {
        $this->password_read();
    }
    // }}}
    // {{{ view_read
    public function view_read($username)
    {
        $locations = Model::load('Locations', 'locations');

        $this->global_tabs->addItem(_("View User Profile"), 'user_profile/'.$this->section.'/view/'.$username);

        $data              = $this->Users->getUserProfile($username);
        $data['location']  = Kohana::config('locale.default_location', 0);

        $isset_profile = False;

        if ($isset_profile = $this->UserProfile->hasUserName($username)) {
            $data          = array_merge($data, $this->UserProfile->getProfile($username));
            $isset_profile = True;
        }

        $data = array_merge($data, array (
                                          'isset_profile' => $isset_profile,
                                          'username'      => $username,
                                          'section'       => $this->section
                                         ));

        $this->layout->content            = new View('view_user_profile', $data);
    }
    // }}}
    // {{{ view_validate_read
    public function view_validate_read()
    {
        if (!Arag_Auth::is_accessible('user/backend') || !Arag_Auth::is_accessible('user_profile/backend')) {
            return False;
        }

        $this->validation->name(0, _("Username"))->add_rules(0, 'required', array($this, '_has_user_name'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ view_read_error
    public function view_read_error()
    {
        $this->_invalid_request();
    }
    // }}}
    // {{{ _check_old_password
    public function _check_old_password($password)
    {
        $status = $this->Users->check($this->username, $password);

        if ($status != Users_Model::USER_OK) {
            return false;
        }

        return true;
    }
    // }}}
    // {{{ _has_user_name
    public function _has_user_name($username)
    {
        return $this->Users->hasUserName($username);
    }
    // }}}
}
