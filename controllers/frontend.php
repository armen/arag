<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Roham Rafii Tehrani <roham.faii@gmail.com>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        $this->validation->message('standard_text', _("%s is not standard text."));
        $this->validation->message('email', _("%s has not right format."));
        $this->validation->message('numeric', _("%s is not numeric"));
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('valid_captcha', _("%s mismatches by text that you typed!"));
    }
    // }}}
    // {{{ index
    // {{{ index_read
    public function index_read()
    {
        $this->layout->content = new View('frontend');
        $this->layout->content->uri  = 'contact_us/frontend/';

        $this->layout->content->name = $this->input->post('contact_name') == Null
                                     ? ''
                                     : $this->input->post('contact_name');

        $this->layout->content->name = $this->input->post('contact_number') == Null
                                     ? ''
                                     : $this->input->post('contact_number');

        $this->layout->content->name = $this->input->post('contact_email') == Null
                                     ? ''
                                     : $this->input->post('contact_email');

        $this->layout->content->name = $this->input->post('contact_title') == Null
                                     ? ''
                                     : $this->input->post('contact_title');

        $this->layout->content->name = $this->input->post('contact_content') == Null
                                     ? ''
                                     : $this->input->post('contact_content');

        $this->layout->content->contact_top_template    = Arag_Config::get('contact.top_template', Null);
        $this->layout->content->contact_bottom_template = Arag_Config::get('contact.bottom_template', Null);
    }
    // }}}
    // {{{ index_write
    public function index_write()
    {
        $contactName    = $this->input->post('contact_name');
        $contactNumber  = $this->input->post('contact_number');
        $contactEmail   = $this->input->post('contact_email');
        $contactTitle   = $this->input->post('contact_title');
        $contactContent = $this->input->post('contact_content');

        $contact = new Contact_Model;
        $result  = $contact->add($contactName, $contactNumber, $contactEmail, $contactTitle, $contactContent);

        $this->layout->content         = new View('result');
        $this->layout->content->result = $result;
    }
    // }}}
    // {{{ index_validate_write
    public function index_validate_write()
    {
        $this->validation->name('contact_name', _("Contact name"))->pre_filter('trim', 'contact_name')
             ->add_rules('contact_name', 'standard_text');

        $this->validation->name('contact_number', _("Contact number"))->pre_filter('trim', 'contact_number')
             ->add_rules('contact_number', 'numeric');

        $this->validation->name('contact_email', _("Contact email"))->pre_filter('trim', 'contant_email')
             ->add_rules('contact_email', 'email');

        $this->validation->name('contact_title', _("Message title"))->pre_filter('trim', 'contact_title')
             ->add_rules('contact_title', 'required', 'standard_text');

        $this->validation->name('contact_content', _("Message content"))->pre_filter('trim', 'contact_content')
             ->add_rules('contact_content', 'required', 'security::xss_clean');

        $this->validation->name('captcha', _("Image's Text"))->add_rules('captcha', 'Captcha_Core::valid_captcha', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ index_write_error
    public function index_write_error()
    {
        $this->index_read();
    }
    // }}}
    // }}}
}
