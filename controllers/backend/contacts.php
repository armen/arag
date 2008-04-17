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
        $this->list_read();
    }
    // }}}
    // {{{ delete
    // {{{ delete_read
    public function delete_read($contact_id)
    {
        $this->global_tabs->addItem(_("Delete"), 'contact_us/backend/contacts/delete/%contact_id%' ,'contact_us/backend/contacts');
        $this->global_tabs->setParameter('contact_id' ,$contact_id);

        $contact     = new Contact_Model;
        $contactInfo = (Array) $contact->getInfo($contact_id);

        $this->layout->content = new View('backend/delete_confirm');
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
        $this->_invalid_request('contact_us/backend/contacts');
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
        $this->_invalid_request('contact_us/backend/contacts');
    }
    // }}}
    // }}}
    // {{{ list
    // {{{ list_read
    public function list_read()
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
    // }}}
    // {{{ view
    // {{{ view_read
    public function view_read($contact_id)
    {
        $this->global_tabs->addItem(_("View"), 'contact_us/backend/contacts/view/%contact_id%' ,'contact_us/backend/contacts');
        $this->global_tabs->setParameter('contact_id' ,$contact_id);

        $contact = new Contact_Model;
        $contactInfo = (Array) $contact->getInfo($contact_id);

        $this->layout->content = new View('backend/view_contact');
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
?>
