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

    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Load the models
        $this->load->model('UserProfile');   
        //$this->load->model('Applications');        
        //$this->load->model('Groups');        
        $this->load->model('Users', NULL, 'user');        
        //$this->load->model('Filters');
        //$this->load->model('Privileges');

        // load global Tabs
        $this->load->component('TabbedBlock', 'global_tabs'); 

        // Default page title
        $this->layout->page_title = 'User Profile';

        // Get the appname
        $this->username = $this->session->get('username');

        // Global tabbedbock
        $this->load->component('TabbedBlock', 'global_tabs');
        $this->global_tabs->setTitle(_("User Profile"));
        $this->global_tabs->addItem(_("Personal Information"), 'user_profile/backend/index'); 
        $this->global_tabs->addItem(_("Change Password"), 'user_profile/backend/password'); 
    }
    // }}}
    // {{{ index_read
    public function index_read()
    {
        $data = array();
        $data = $this->Users->getUserProfile($this->username);
        
        if ($isset_profile = $this->UserProfile->hasUserName($this->username)) {
            $data = array_merge($data, $this->UserProfile->getProfile($this->username));
            
            /*if ($data['pan'] != NULL) {
                $data['pan'] = $this->panSplitter($data['pan']);
            }*/
        }

        $data = array_merge($data, array (
                                          'flagsaved'     => $this->session->get_once('user_profile_profile_saved'),
                                          'isset_profile' => $isset_profile,
                                          'username'      => $this->username,
                                         ));

        $this->layout->content = new View('user_profile', $data);
    }
    // }}}
    // {{{ index_write
    public function index_write()
    {
        $province    = $this->input->post('province', true);
        $city        = $this->input->post('city', true);
        $address     = $this->input->post('address', true);
        $phone       = $this->input->post('phone', true);
        $cellphone   = $this->input->post('cellphone', true);
        $postal_code = $this->input->post('postal_code', true);
        
        if ($this->UserProfile->hasUserName($this->username)) {
            $this->UserProfile->editProfile($province, $city, $address, $phone, $cellphone, $postal_code, $this->username);
        } else {
            $this->UserProfile->insertProfile($province, $city, $address, $phone, $cellphone, $postal_code, $this->username);
        }

        $this->session->set('user_profile_profile_saved', true);

        $this->index_read();
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
        $this->layout->content = new View('change_password', array('flagsaved' => $this->session->get_once('user_profile_password_saved')));
    }
    // }}}
    // {{{ password_write
    public function password_write()
    {
        $newpassword = $this->input->post('newpassword');
        $oldpassword = $this->input->post('oldpassword');

        $this->Users->changePassword($this->username, '', $newpassword);

        $this->session->set('user_profile_password_saved', true);

        $this->password_read();
    }
    // }}}
    // {{{ password_write_error
    public function password_write_error()
    {
        $this->password_read();
    }
    // }}}
    // {{{ panSplitter
    public function panSplitter($pan)
    {
        if (is_array($pan)) {
            return implode('', $pan);
        } else {
            return str_split($pan, 4);
        }
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

}
?>
