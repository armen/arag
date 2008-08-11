<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Roham Rafii Tehrani <roham.rafii@gmail.com                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Contact_Model extends Model
{
    // {{{ Properties

    public $tableContacts;

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Set table name
        $this->tableContacts = 'contact_us_contacts';
    }
    // }}}
    // {{{ add
    public function add($contactName, $contactNumber, $contactEmail, $contactTitle, $contactContent)
    {
        $row = array('name'        => $contactName,
                     'tel'         => $contactNumber,
                     'email'       => $contactEmail,
                     'title'       => $contactTitle,
                     'content'     => $contactContent,
                     'create_date' => time());

        $result = $this->db->insert($this->tableContacts, $row) == False ? false : True;
        return $result;
    }
    // }}}
    // {{{ getInfo
    public function getInfo($id)
    {
        // Select * FROM tableNameProducts WHERE
        $this->db->select('id, create_date, name, tel, email, title, content');
        $this->db->from($this->tableContacts);

        return $this->db->where('id', $id)->get()->result()->current();
    }
    // }}}
    // {{{ getAll
    public function getAll()
    {
        $this->db->select('id, create_date, name, tel, email, title, content');
        $this->db->from($this->tableContacts);

        $query = $this->db->get();
        return $query->result(False);
    }
    // }}}
    // {{{ delete
    public function delete($id)
    {
        $this->db->delete($this->tableContacts, Array('id' => $id));
    }
    // }}}
    // {{{ hasContact
    public function hasContact($id)
    {
        $this->db->select('count(id) as count')->from($this->tableContacts);
        $this->db->where('id', $id);

        return (boolean) $this->db->get()->current()->count;
    }
    // }}}
    // {{{ getCreateDate
    public function getCreateDate($row)
    {
        return format::date($row['create_date']);
    }
    // }}}
    // {{{ getContent
    public function getContent($row)
    {
        $truncateLength = (int)Arag_Config::get('contact.truncate_length', Null);
        $content = $truncateLength > 0 ? substr($row['content'], 0, $truncateLength) : $row['content'];

        return $content;
    }
    // }}}
}
