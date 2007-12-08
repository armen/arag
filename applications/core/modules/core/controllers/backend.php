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
    function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = _("Core Settings");

        // Global tabbedbock
        $this->load->component('TabbedBlock', 'global_tabs');
        $this->global_tabs->setTitle(_("Core Settings"));
        $this->global_tabs->addItem(_("Email Settings"), 'core/backend/email');
    }
    // }}}
    // {{{ email_read
    public function email_read()
    {   
        $data = Arag_Config::get("email_settings", array());

        $data['saved'] = $this->session->get_once('core_settings_email_saved');

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

        $this->session->set('core_settings_email_saved', true);

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
