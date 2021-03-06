<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Messengers_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = _("Messenger's Status");

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Messenger's Status"));
        $this->global_tabs->addItem(_("Add ID"), 'messengers_status/backend/messengers/add');

        // Validation Messages
        $this->validation->message('required', _("%s is required"));
        $this->validation->message('email', _("%s must be a valid email address"));
    }
    // }}}
    // {{{ add
    // {{{ add_read
    public function add_read()
    {
        $data = Arag_Config::get('messenger_status_settings_add', array());

        $data['saved']   = $this->session->get_once('messenger_status_settings_add_saved');
        $data['types']   = Kohana::config('messengers.types');
        $data['details'] = Kohana::config('messengers.details');

        $this->layout->content = new View('backend/add', $data);
    }
    // }}}
    // {{{ add_write
    public function add_write()
    {
        $settings = array (
                           'id'      => $this->input->post('id'),
                           'subject' => $this->input->post('subject'),
                           'type'    => $this->input->post('type')
                          );

        if (empty($settings['id'])) {
            $settings = Array();
        }

        Arag_Config::set('messenger_status_settings_add', $settings);

        $this->session->set('messenger_status_settings_add_saved', true);
        $this->add_read();
    }
    // }}}
    // {{{ add_validate_write
    public function add_validate_write()
    {
        $this->validation->name('id', _("ID"))->pre_filter('trim', 'id')->add_rules('id.*', 'required', 'valid::email');
        $this->validation->name('subject', _("Subject"))->pre_filter('trim', 'subject')->add_rules('subject.*', 'standard_text');
        $this->validation->name('type', _("Type"))->add_rules('type.*', 'equals[yahoo]');

        return $this->validation->validate();
    }
    // }}}
    // {{{ add_write_error
    public function add_write_error()
    {
        $this->add_read();
    }
    // }}}
    // }}}
}
