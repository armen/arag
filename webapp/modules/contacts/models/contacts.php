<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Contacts_Model extends Model
{
    // {{{ Properties

    private $tableName;

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Set the table name
        $this->tableName = 'contacts';
    }
    // }}}
    // {{{ createContact
    public function createContact($namespace, $referenceId, $author, $data = array())
    {
        foreach ($data as $datum) {
            $row = Array('namespace'    => $namespace,
                         'reference_id' => $referenceId,
                         'type'         => $datum['type'],
                         'value'        => $datum['value'],
                         'username'     => $author,
                         'create_date'  => time(),
                         'created_by'   => $author,
                         'modify_date'  => 0,
                         'modified_by'  => Null);

            $this->db->insert($this->tableName, $row);
        }
    }
    // }}}
    // {{{ updateContact
    public function updateContact($namespace, $referenceId, $data = array())
    {
        $session = New Session();
        foreach ($data as $datum) {
            if (isset($datum['id'])) {
                if (isset($datum['value']) && $datum['value'] != '') {
                    $this->db->where('id', $datum['id']);
                    $this->db->update($this->tableName, array('value' => $datum['value'], 'type' => $datum['type'], 'modify_date' => time(),
                                                              'modified_by' => $session->get('user.username')));
                } else {
                    $this->db->where('id', $datum['id']);
                    $this->db->delete($this->tableName);
                }
            } else {
                $this->createContact($namespace, $referenceId, $session->get('user.username'), array($datum));
            }
        }
    }
    // }}}
    // {{{ editContact
    public function editContact($id, $value, $author)
    {
        $row = Array(
                     'value'        => $value,
                     'modify_date'  => time(),
                     'modified_by'  => $author
                    );

        $this->db->where('id', $id);
        $this->db->update($this->tableName, $row);
    }
    // }}}
    // {{{ deleteContact
    public function deleteContact($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));
    }
    // }}}
    // {{{ & getContact
    public function & getContact($id)
    {
        $this->db->select('*');

        $query = $this->db->getwhere($this->tableName, Array('id' => $id));

        $retval = (array) $query->current();
        return $retval;
    }
    // }}}
    // {{{ getContacts
    public function getContacts($namespace, $referenceId = False, $username = Null)
    {
        $query = $this->db->select('*');

        if ($referenceId !== False) {
            $query->where('reference_id', $referenceId);
        }

        if ($username !== Null) {
            $query->where('username', $username);
        }

        $retval = $query->where('namespace', $namespace)->orderby('create_date', 'asc')->get($this->tableName)->result();

        return $retval;
    }
    // }}}
    // {{{ List callbacks
    // {{{ getCreateDate
    public function getCreateDate($row)
    {
        return ($row['create_date']) ? date('Y-m-d H:i:s', $row['create_date']) : '-';
    }
    // }}}
    // {{{ getCreatedBy
    public function getCreatedBy($row)
    {
        return ($row['created_by']) ? $row['created_by'] : '-';
    }
    // }}}
    // {{{ getModifyDate
    public function getModifyDate($row)
    {
        return ($row['modify_date']) ? date('Y-m-d H:i:s', $row['modify_date']) : '-';
    }
    // }}}
    // {{{ getModifiedBy
    public function getModifiedBy($row)
    {
        return ($row['modified_by']) ? $row['modified_by'] : '-';
    }
    // }}}
    // }}}
}
