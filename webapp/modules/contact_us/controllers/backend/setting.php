<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Roham Rafii Tehrani <roham.rafii@gmail.com>                     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Setting_Controller extends Backend_Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();
    }
    // }}}
    // {{{ top_template
    // {{{ top_template_read
    public function top_template_read()
    {
        $this->layout->content = new View('backend/template_editor');

        $this->layout->content->template = Arag_Config::get('contact.top_template', '');
        $this->layout->content->uri      = 'contact_us/backend/setting/top_template';
    }
    // }}}
    // {{{ top_template_write
    public function top_template_write()
    {
        Arag_Kohana::config_set('contact.top_template', $this->input->post('template'));

        url::redirect('contact_us/backend/setting/bottom_template');
    }
    // }}}
    // {{{ top_template_validate_write
    public function top_template_validate_write()
    {
        $this->validation->name('template', _("Top Template"))->add_rules('template', 'required')
             ->post_filter('security::xss_clean', 'template');

        return $this->validation->validate();
    }
    // }}}
    // {{{ top_template_write_error
    public function top_template_write_error()
    {
        $this->top_template_read();
    }
    // }}}
    // }}}
    // {{{ bottom_template
    // {{{ bottom_template_read
    public function bottom_template_read()
    {
        $this->layout->content = new View('backend/template_editor');

        $this->layout->content->template = Arag_Config::get('contact.bottom_template', '');
        $this->layout->content->uri      = 'contact_us/backend/setting/bottom_template';
    }
    // }}}
    // {{{ bottom_template_write
    public function bottom_template_write()
    {
        Arag_Kohana::config_set('contact.bottom_template', $this->input->post('template'));

        url::redirect('contact_us/backend/setting/bottom_template');
    }
    // }}}
    // {{{ bottom_template_validate_write
    public function bottom_template_validate_write()
    {
        $this->validation->name('template', _("Bottom Template"))->add_rules('template', 'required')
             ->post_filter('security::xss_clean', 'template');

        return $this->validation->validate();
    }
    // }}}
    // {{{ bottom_template_write_error
    public function bottom_template_write_error()
    {
        $this->bottom_template_read();
    }
    // }}}
    // }}}
    // {{{ view
    // {{{ view_read
    public function view_read($contact_id)
    {
        $this->global_tabs->addItem(_("View"), 'contact_us/backend/contacts/view/%contact_id%' ,'contact_us/backend/contacts');
        $this->global_tabs->setParameter('contact_id' ,$contact_id);

        $contact     = new Contact_Model;
        $contactInfo = (Array) $contact->getInfo($contact_id);

        $this->layout->content               = new View('backend/view_contact');
        $this->layout->content->contact_info = $contactInfo;
        $this->layout->content->uri          = 'contact_us/backend/contacts/delete/'.$contact_id;
    }
    // }}}
    // {{{ view_validation_read
    public function view_validate_read()
    {
        $this->validation->name(0, _("Contact Id"))->add_rules(0, 'required', 'numeric', array($this, '_check_contact'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ view_read_error
    public function view_read_error()
    {
        $this->view_read();
    }
    // }}}
    // }}}
    // {{{ Callbacks
    // {{{ _check_product
    public function _check_contact($id)
    {
        $contact = new Contact_Model;

        return $contact->hasContact($id);
    }
    // }}}
    // }}}
}
