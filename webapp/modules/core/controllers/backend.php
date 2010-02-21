<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = _("Core Settings");

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Core Settings"));
        $this->global_tabs->addItem(_("Email Settings"), 'core/backend/email');

        // Validation Messages
        $this->validation->message('numeric', _("%s should be numeric"));
        $this->validation->message('required', _("%s is required"));
        $this->validation->message('email', _("%s must be a valid email address"));
        $this->validation->message('alpha_numeric', _("%s must be alph-numeric."));
    }
    // }}}
    // {{{ email_read
    public function email_read()
    {
        $data = Arag_Config::get("email_settings", array(), 'core', False, Kohana::config('arag.master_appname'));

        $data['saved']          = $this->session->get_once('core_settings_email_saved');
        $data['authenticators'] = array('PLAIN' => _("PLAIN"), 'LOGIN' => _("LOGIN"), 'CRAMMD5' => _("CRAMMD5"));
        $data['encryptions']    = array('NONE' => _("NONE"), 'TLS' => _("TLS"), 'SSL' => _("SSL"));

        $this->layout->content = new View('backend/settings_email', $data);
    }
    // }}}
    // {{{ email_write
    public function email_write()
    {
        $data     = Arag_Config::get("email_settings", array(), 'core', False, Kohana::config('arag.master_appname'));
        $settings = array (
                           'smtpserver'     => $this->input->post('smtpserver'),
                           'sender'         => $this->input->post('sender'),
                           'smtpport'       => $this->input->post('smtpport'),
                           'authenticator'  => $this->input->post('authenticator'),
                           'authentication' => false,
                           'encryption'     => $this->input->post('encryption')
                          );

        if ($this->input->post('username')) {
            $settings = array_merge($settings, array(
                                                     'username'       => $this->input->post('username'),
                                                     'password'       => $this->input->post('password'),
                                                     'authentication' => true
                                                    ));
        }

        // Merge with user settings
        $settings = array_merge($data, $settings);

        Arag_Config::set('email_settings', $settings, 'core', Kohana::config('arag.master_appname'));

        $this->session->set('core_settings_email_saved', true);

        $this->email_read();
    }
    // }}}
    // {{{ email_validate_write
    public function email_validate_write()
    {
        $this->validation->name('smtpserver', _("SMTP server"))->pre_filter('trim', 'smtpserver')
             ->add_rules('smtpserver', 'required');

        $this->validation->name('smtpport', _("SMTP Port"))->pre_filter('trim', 'smtpport')
             ->add_rules('smtpport', 'required', 'valid::numeric');

        $this->validation->name('sender', _("Sender's email"))->pre_filter('trim', 'sender')
             ->add_rules('sender', 'required', 'valid::email');

        $this->validation->name('username', _("Username"))->pre_filter('trim', 'username');

        $this->validation->name('authenticator', _("Authenticator"))->pre_filter('trim', 'authenticator')
             ->add_rules('authenticator', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ email_write_error
    public function email_write_error()
    {
        $this->email_read();
    }
    // }}}
}
