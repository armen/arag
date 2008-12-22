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

    public $default_city;
    public $default_country;
    public $default_province;

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Load the models
        $this->UserProfile = new UserProfile_Model;
        $this->Users = Model::load('Users', 'user');

        // load global Tabs
        $this->globals_tabs = new TabbedBlock_Component('global_tabs');

        // Default page title
        $this->layout->page_title = 'User Profile';

        // Default Locations
        $this->default_city     = Kohana::config('config.default_city');
        $this->default_country  = Kohana::config('config.default_country');
        $this->default_province = Kohana::config('config.default_province');

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
    public function index_read()
    {
        $data = array();

        $data = $this->Users->getUserProfile($this->username);

        $isset_profile = False;

        if ($isset_profile = $this->UserProfile->hasUserName($this->username)) {
            $data          = array_merge($data, $this->UserProfile->getProfile($this->username));
            $isset_profile = True;

        }

        $provinces = $this->UserProfile->getProvinces();

        $cities = array();

        if (!isset($data['province']) || $data['province'] == 0) {
            $cities = $this->UserProfile->getCities($this->default_province);
            $data['province'] = $this->default_province;
        } else {
            $cities = $this->UserProfile->getCities($data['province']);
        }

        $provinces = $this->UserProfile->getProvinces();

        $cities = array();

        if (!isset($data['province']) || $data['province'] == 0) {
            $cities = $this->UserProfile->getCities($this->default_province);
            $data['province'] = $this->default_province;
        } else {
            $cities = $this->UserProfile->getCities($data['province']);
        }

        $data = array_merge($data, array (
                                          'flagsaved'     => $this->session->get_once('user_profile_profile_saved'),
                                          'isset_profile' => $isset_profile,
                                          'username'      => $this->username,
                                          'section'       => $this->section,
                                          'countries'     => $this->UserProfile->getCountries(),
                                          'provinces'     => $provinces,
                                          'cities'        => $cities,
                                          'defaults'      => array('city' => $this->default_city, 'province' => $this->default_province,
                                                                   'country' => $this->default_country)
                                         ));

        $this->layout->content = new View($this->section.'_user_profile', $data);
    }
    // }}}
    // {{{ index_write
    public function index_write()
    {
        $data        = $this->Users->getUserProfile($this->username);
        $province    = $this->input->post('province', Null, true);
        $city        = $this->input->post('city', Null, true);
        $country     = $this->input->post('country', Null, true);
        $address     = $this->input->post('address', Null, true);
        $phone       = $this->input->post('phone', Null, true);
        $cellphone   = $this->input->post('cellphone', Null, true);
        $postal_code = $this->input->post('postal_code', Null, true);

        if ($this->UserProfile->hasUserName($this->username)) {
            $this->UserProfile->editProfile($province, $city, $address, $phone, $cellphone, $postal_code, $this->username, $country);
        } else {
            $this->UserProfile->insertProfile($province, $city, $address, $phone, $cellphone, $postal_code, $this->username, $data['name'], $data['lastname'], $country);
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

        $this->validation->name('address', _("Address"))->add_rules('address', 'required');

        $this->validation->name('country', _("Country"))->add_rules('country', 'required');

        if ($this->input->post('country') == $this->default_country) {
            $this->validation->name('city', _("City"))->add_rules('city', 'required');
            $this->validation->name('province', _("Province"))->add_rules('province', 'required');
        }

        $this->validation->name('postal_code', _("Postal Code"))->add_rules('postal_code', 'required', 'valid::numeric', 'length[5, 10]');

        return $this->validation->validate();
    }
    // }}}
    // {{{ index_write_error
    public function index_write_error()
    {
        $this->index_read();
    }
    // }}}
    // {{{ password_read
    public function password_read()
    {
        $this->layout->content = new View($this->section.'_change_password', array(
                                                                                   'flagsaved' => $this->session->get_once('user_profile_password_saved'),
                                                                                   'section'   => $this->section
                                                                                  ));
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
        $this->global_tabs->addItem(_("View User Profile"), 'user_profile/'.$this->section.'/view/'.$username);
        $data = array();

        $data = $this->Users->getUserProfile($username);

        $isset_profile = False;

        if ($isset_profile = $this->UserProfile->hasUserName($username)) {
            $data          = array_merge($data, $this->UserProfile->getProfile($username));
            $isset_profile = True;
        }

        $provinces = $this->UserProfile->getProvinces();

        $cities = array();

        if (!isset($data['province']) || $data['province'] == 0) {
            $cities = $this->UserProfile->getCities($this->default_province);
            $data['province'] = $this->default_province;
        } else {
            $cities = $this->UserProfile->getCities($data['province']);
        }

        $data = array_merge($data, array (
                                          'isset_profile' => $isset_profile,
                                          'username'      => $username,
                                          'section'       => $this->section,
                                          'countries'     => $this->UserProfile->getCountries(),
                                          'provinces'     => $provinces,
                                          'cities'        => $cities,
                                          'defaults'      => array('city' => $this->default_city, 'province' => $this->default_province,
                                                                   'country' => $this->default_country)
                                         ));

        $this->layout->content = new View('view_user_profile', $data);
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
