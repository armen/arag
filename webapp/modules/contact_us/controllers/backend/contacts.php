<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Roham Rafii Tehrani <roham.rafii@gmail.com>                     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Contacts_Controller extends Backend_Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();
    }
    // }}}
    // {{{ index
    public function index()
    {
        // Load Models and Components
        $contact     = new Contact_Model;
        $contactList = new PList_Component('contact');

        $contactList->setResource($contact->getAll());

        $contactList->addColumn('title', _("Message Title"));
        $contactList->addColumn('contact.getCreateDate', _("Create Date"), PLIST_Component::VIRTUAL_COLUMN);
        $contactList->addColumn('contact.getContent', _("Message Content"), Plist_Component::VIRTUAL_COLUMN);

        $contactList->addAction('contact_us/backend/contacts/view/#id#', _("View"), 'view_action');
        $contactList->addAction('contact_us/backend/contacts/delete/#id#', _("Delete"), 'delete_action');

        $this->layout->content = new View('backend/list_contacts');
    }
    // }}}
    // {{{ delete
    // {{{ delete_read
    public function delete_read($contact_id)
    {
        $this->global_tabs->addItem(_("View"), 'contact_us/backend/contacts/view/%contact_id%' ,'contact_us/backend/contacts');
        $this->global_tabs->setParameter('contact_id' ,$contact_id);

        $contact     = new Contact_Model;
        $contactInfo = (Array) $contact->getInfo($contact_id);

        if ($contactInfo['email'] != '') {
            $this->global_tabs->addItem(_("Reply"), 'contact_us/backend/contacts/reply/%contact_id%' ,'contact_us/backend/contacts');
            $this->global_tabs->setParameter('contact_id' ,$contact_id);
        }


        $this->global_tabs->addItem(_("Delete"), 'contact_us/backend/contacts/delete/%contact_id%' ,'contact_us/backend/contacts');
        $this->global_tabs->setParameter('contact_id' ,$contact_id);

        $this->layout->content                = new View('backend/delete_confirm');
        $this->layout->content->contact_id    = $contact_id;
        $this->layout->content->contact_title = $contactInfo['title'];
    }
    // }}}
    // {{{ delete_validate_read
    public function delete_validate_read()
    {
        $this->validation->name(0, _("Contact Id"))->add_rules(0, 'required', 'numeric', array($this, '_check_contact'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_read_error
    public function delete_read_error()
    {
        $this->_invalid_request('contact_us/backend/contacts', _("Invalid contanct ID"));
    }
    // }}}
    // {{{ delete_write
    public function delete_write()
    {
        $contact_id = $this->input->post('contact_id');

        $contact = new Contact_Model;
        $contact->delete($contact_id);

        url::redirect('contact_us/backend/contacts');
    }
    // }}}
    // {{{ delete_validate_write
    public function delete_validate_write()
    {
        $this->validation->name('contact_id', _("Contact Id"))->add_rules('contact_id', 'required', 'numeric', array($this, '_check_contact'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_write_error
    public function delete_write_error()
    {
        $this->_invalid_request('contact_us/backend/contacts', _("Invalid contact ID"));
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

        if ($contactInfo['email'] != '') {
            $this->global_tabs->addItem(_("Reply"), 'contact_us/backend/contacts/reply/%contact_id%' ,'contact_us/backend/contacts');
            $this->global_tabs->setParameter('contact_id' ,$contact_id);
        }

        $this->global_tabs->addItem(_("Delete"), 'contact_us/backend/contacts/delete/%contact_id%' ,'contact_us/backend/contacts');
        $this->global_tabs->setParameter('contact_id' ,$contact_id);

        $contactInfo['create_date'] = $contact->getCreateDate($contactInfo);

        $this->layout->content               = new View('backend/view_contact');
        $this->layout->content->contact_info = $contactInfo;
        $this->layout->content->contact_id   = $contact_id;
        $this->layout->content->message      = $this->session->get_once('contact_us_message', False);
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
        $this->_invalid_request('contact_us/backend/contacts', _("Invalid contanct ID"));
    }
    // }}}
    // }}}
    // {{{ reply
    // {{{ reply_read
    public function reply_read($contact_id)
    {
        $this->global_tabs->addItem(_("View"), 'contact_us/backend/contacts/view/%contact_id%' ,'contact_us/backend/contacts');
        $this->global_tabs->setParameter('contact_id' ,$contact_id);

        $this->global_tabs->addItem(_("Reply"), 'contact_us/backend/contacts/reply/%contact_id%' ,'contact_us/backend/contacts');
        $this->global_tabs->setParameter('contact_id' ,$contact_id);

        $this->global_tabs->addItem(_("Delete"), 'contact_us/backend/contacts/delete/%contact_id%' ,'contact_us/backend/contacts');
        $this->global_tabs->setParameter('contact_id' ,$contact_id);


        $contact     = new Contact_Model;
        $contactInfo = (Array) $contact->getInfo($contact_id);

        $contactInfo['create_date'] = $contact->getCreateDate($contactInfo);

        $contactInfo['content'] = explode("\n", $contactInfo['content']);

        foreach($contactInfo['content'] as $key => $line) {
            $contactInfo['content'][$key] = '> ' . $line;
        }

        $contactInfo['content'] = implode("\n", $contactInfo['content']);
        $contactInfo['title']   = 'Re: ' . $contactInfo['title'];

        $this->layout->content             = new View('backend/reply_contact');
        $this->layout->content->subject    = $contactInfo['title'];
        $this->layout->content->email      = $contactInfo['email'];
        $this->layout->content->contact_id = $contact_id;
        $this->layout->content->body       = $contactInfo['content'];
    }
    // }}}
    // {{{ reply_validation_read
    public function reply_validate_read()
    {
        $this->validation->name(0, _("Contact Id"))->add_rules(0, 'required', 'numeric', array($this, '_check_contact'), array($this, '_has_email'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ reply_read_error
    public function reply_read_error()
    {
        $this->_invalid_request('contact_us/backend/contacts', _("Invalid contanct ID"));
    }
    // }}}
    // {{{ reply_write
    public function reply_write()
    {
        $body       = $this->input->post('body');
        $email      = $this->input->post('email');
        $subject    = $this->input->post('subject');
        $contact_id = $this->input->post('contact_id');

        $multisite = Model::load('MultiSite', 'multisite');

        // Send an email to verify the user
        $settings = Arag_Config::get('email_settings', NULL, 'core', False, Kohana::config('arag.master_appname'));

        $lang      = Kohana::config('locale.lang');
        $direction = Kohana::config('locale.languages_direction.'.$lang);
        $align     = ($direction == 'rtl') ? 'right' : 'left';


        $settings['template'] = '<div style="direction:'.$lang.';text-align:'.$align.';">'.nl2br($body).'</div>';
        $settings['subject']  = $subject;

        $strings  = array();

        try {
            $multisite->sendEmail($email, $strings, $settings);
            $message = _("Email Sent successfuly!");
            $contact = new Contact_Model;
            $contact->updateContent($contact_id, $body . '
            --- ' . _("Sent by: ") . $this->session->get('user.username') . ' ---');

        } catch(Swift_Exception $e) {
            // Shit, there was an error here!
            Kohana::log('error', $e->getMessage());
            $message = _("There was a problem in sending email");
        }

        $this->session->set('contact_us_message', $message);

        url::redirect('contact_us/backend/contacts/view/'.$contact_id);
    }
    // }}}
    // {{{ reply_validation_write
    public function reply_validate_write()
    {
        $this->validation->name('contact_id', _("Contact Id"))->add_rules('contact_id', 'required', 'numeric', array($this, '_check_contact'),
                                                                          array($this, '_has_email'));

        $this->validation->name('subject', _("Subject"))->pre_filter('trim', 'subject')->add_rules('subject', 'required', 'standard_text');

        $this->validation->name('body', _("Body"))->pre_filter('trim', 'body')->add_rules('body', 'required', 'security::xss_clean');

        return $this->validation->validate();
    }
    // }}}
    // {{{ reply_write_error
    public function reply_write_error()
    {
        $this->reply_read($this->input->post('contact_id'));
    }
    // }}}
    // }}}
    // {{{ Callbacks
    // {{{ _check_contact
    public function _check_contact($id)
    {
        $contact = new Contact_Model;

        return $contact->hasContact($id);
    }
    // }}}
    // {{{ _has_email
    public function _has_email($id)
    {
        $contact = new Contact_Model;

        $contactInfo = (Array) $contact->getInfo($id);

        return $contactInfo['email'] != '' ? true : false;
    }
    // }}}
    // }}}
}
