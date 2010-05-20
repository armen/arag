<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Contacts_Controller extends Controller
{
    // {{{ _contacts_add
    public function _contacts_add($reference_id)
    {
        $key          = $this->input->post('key');
        $namespace    = $this->session->get('contacts.'.$key.'.namespace');
        $uri          = $this->session->get('contacts.'.$key.'.uri');
        $contacts     = $this->input->post('contacts');
        $types        = $this->input->post('types');
        $location     = locations::get($this->input->post('location', Null, true));
        $data         = Array();

        foreach ($contacts as $key => $contact) {
            if (isset($contact) && trim($contact) != '') {
                $data[] = array('type' => $types[$key], 'value' => $contact);
            }
        }

        $data[] = array('type' => 'location', 'value' => $location);


        if (!$namespace || !$reference_id || !$uri) {
            $this->_invalid_request();
        }

        $contacts  = Model::load('Contacts', 'contacts');

        $contacts->createContact($namespace, $reference_id, $this->session->get('user.username'), $data);

        url::redirect($uri);
    }
    // }}}
    // {{{ _contacts_add_validate
    public function _contacts_add_validate()
    {
        return true;
        //return $this->validation->validate();
    }
    // }}}
    // {{{ _contacts_edit
    public function _contacts_edit()
    {
        $key          = $this->input->post('key');
        $namespace    = $this->session->get('contacts.'.$key.'.namespace');
        $uri          = $this->session->get('contacts.'.$key.'.uri');
        $reference_id = $this->session->get('contacts.'.$key.'.reference_id');
        $contacts     = $this->input->post('contacts');
        $types        = $this->input->post('types');
        $ids          = $this->input->post('ids');
        $locationid   = $this->input->post('location_id');
        $location     = locations::get($this->input->post('location', Null, true));
        $data         = Array();


        foreach ($contacts as $key => $contact) {
            if ((isset($contact) && trim($contact) != '') || isset($ids[$key-1])) {
                $data[] = array('type' => $types[$key], 'value' => $contact, 'id' => isset($ids[$key-1]) ? $ids[$key-1] : Null);
            }
        }

        $data[] = array('type' => 'location', 'value' => $location, 'id' => $locationid);

        if (!$namespace || !$reference_id || !$uri) {
            $this->_invalid_request();
        }

        $contacts  = Model::load('Contacts', 'contacts');

        $contacts->updateContact($namespace, $reference_id, $data);

        url::redirect($uri);
    }
    // }}}
    // {{{ _contacts_edit_validate
    public function _contacts_edit_validate()
    {
        return true;
        //return $this->validation->validate();
    }
    // }}}

}
