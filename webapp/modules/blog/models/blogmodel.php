<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class BlogModel extends Model 
{
    // {{{ Properties
    
    var $tableName;

    // }}}
    // {{{ BlogModel
    function BlogModel()
    {
        parent::Model();

        // Connecting to the database
        $this->load->database();

        // Set the table name
        $this->tableName = 'blog_entries';
    }
    // }}}
    // {{{ createEntry
    function createEntry($subject, $entry)
    {
        $row = Array('subject'     => $subject, 
                     'entry'       => $entry, 
                     'author'      => 'admin',
                     'create_date' => time(),
                     'modify_date' => 0);

        $this->db->insert($this->tableName, $row);
    }
    // }}}
    // {{{ editEntry
    function editEntry($id, $subject, $entryBody)
    {
        $entry = $this->getEntry($id);

        $row = Array('subject'     => $subject, 
                     'entry'       => $entryBody, 
                     'author'      => $entry['author'],
                     'create_date' => $entry['create_date'],
                     'modify_date' => time());
       
       $this->db->where('id', $id);
       $this->db->update($this->tableName, $row);
    }
    // }}}
    // {{{ deleteEntry
    function deleteEntry($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));
    }
    // }}}
    // {{{ & getEntry
    function & getEntry($id)
    {
        $this->db->select('id, subject, entry, author, create_date');
        $query = $this->db->getwhere($this->tableName, Array('id' => $id));

        $retval = $query->first_row('array');
        return $retval;
    }
    // }}}
    // {{{ & getEntries
    function & getEntries()
    {
        $this->db->select('id, subject, entry, author, create_date');
        $this->db->orderby('create_date', 'desc');
        $query = $this->db->get($this->tableName);

        $retval = $query->result_array();
        return $retval;
    }
    // }}}
    // {{{ getDate
    function getDate($row)
    {
        return date('Y-m-d H:i:s', $row['create_date']);
    }
    // }}}
}

?>
