<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Settings_Controller extends Backend_Controller 
{
    // {{{ index_read
    public function index_read()
    {   
        $data          = Array();
        $data['limit'] = Arag_Config::get("limit");
        $data['saved'] = $this->session->get_once('multi_site_settings_limit_saved');

        $this->layout->content = new View('backend/settings_limit', $data);
    }
    // }}}
    // {{{ index_write
    public function index_write()
    {

        Arag_Config::set('limit', $this->input->post('limit'));

        $this->session->set('multi_site_settings_limit_saved', true);

        $this->index_read();

    }
    // }}}
    // {{{ index_write_error
    public function index_write_error()
    {
        $this->index_read();
    }
    // }}}
    // {{{ expire_time_read
    public function expire_time_read()
    {   
        $data           = Array();
        $data['expire'] = Arag_Config::get("expire");
        $data['saved']  = $this->session->get_once('multi_site_settings_expire_saved');

        $this->layout->content = new View('backend/settings_expire', $data);
    }
    // }}}
    // {{{ expire_time_write
    public function expire_time_write()
    {

        Arag_Config::set('expire', $this->input->post('expire'));

        $this->session->set('multi_site_settings_expire_saved', true);

        $this->expire_time_read();

    }
    // }}}
    // {{{ expire_time_write_error
    public function expire_time_write_error()
    {
        $this->expire_time_read();
    }
    // }}}
    // {{{ password_read
    public function password_read()
    {   
        $data           = Array();
        $data['length'] = Arag_Config::get("passlength");
        $data['saved']  = $this->session->get_once('multi_site_settings_pass_length_saved');

        $this->layout->content = new View('backend/settings_password', $data);
    }
    // }}}
    // {{{ password_write
    public function password_write()
    {

        Arag_Config::set('passlength', $this->input->post('length'));

        $this->session->set('multi_site_settings_pass_length_saved', true);

        $this->password_read();

    }
    // }}}
    // {{{ password_write_error
    public function password_write_error()
    {
        $this->password_read();
    }
    // }}}
    // {{{ privileges_read
    public function privileges_read()
    {   
        $data = Array();
        
        if (Arag_Config::get("adminpri") != NULL) {
            $data['adminpri'] = implode(" ", Arag_Config::get("adminpri"));
        }

        if (Arag_Config::get("adminpri") != NULL) {
            $data['anonypri'] = implode(" ", Arag_Config::get("anonypri"));
        }

        $data['saved'] = $this->session->get_once('multi_site_settings_privileges_saved');

        $this->layout->content = new View('backend/settings_privileges', $data);
    }
    // }}}
    // {{{ privileges_write
    public function privileges_write()
    {
        Arag_Config::set('adminpri', array_unique(explode(" ", preg_replace('/\s+/', ' ', trim($this->input->post('adminpri', true))))));
        Arag_Config::set('anonypri', array_unique(explode(" ", preg_replace('/\s+/', ' ', trim($this->input->post('anonypri', true))))));

        $this->session->set('multi_site_settings_privileges_saved', true);

        $this->privileges_read();
    }
    // }}}
    // {{{ privileges_write_error
    public function privileges_write_error()
    {
        $this->privileges_read();
    }
    // }}}
    // {{{ email_read
    public function email_read()
    {   
        $data = Arag_Config::get("email_settings", array());

        $data['saved'] = $this->session->get_once('multi_site_settings_email_saved');

        $this->layout->content = new View('backend/settings_email', $data);
    }
    // }}}
    // {{{ email_write
    public function email_write()
    {
        $settings = array (
                           'smtpserver' => $this->input->post('smtpserver'),
                           'sender'     => $this->input->post('sender'),
                           'subject'    => $this->input->post('subject'),
                           'template'   => $this->input->post('template'),
                           'smtpport'   => $this->input->post('smtpport')
                          );

        if ($this->input->post('username')) {
            $settings = array_merge($settings, array(
                                                     'username'       => $this->input->post('username'),
                                                     'password'       => $this->input->post('password'),
                                                     'authentication' => true
                                                    ));
        }

        Arag_Config::set('email_settings', $settings);

        $this->session->set('multi_site_settings_email_saved', true);

        $this->email_read();
    }
    // }}}
    // {{{ email_write_error
    public function email_write_error()
    {
        $this->email_read();
    }
    // }}}
}
?>
