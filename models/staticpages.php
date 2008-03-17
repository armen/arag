<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author:   Sasan Rose <sasan.rose@gmail.com>                             |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class StaticPages_Model extends Model 
{
    // {{{ Properties
    
    public $tableName;

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Set the table name
        $this->tableName = 'static_pages';
    }
   // }}}
    // {{{ createPage
    public function createPage($author, $subject, $page)
    {
        $row = Array('author'      => $author, 
                     'create_date' => time(),
                     'modify_date' => time(),
                     'subject'     => $subject,
                     'page'        => $page);

        $this->db->insert($this->tableName, $row);
    }
    // }}}
    // {{{ editPage
    public function editPage($id, $subject, $page)
    {
        $entry = $this->getPage($id);

        $row = Array('subject'     => $subject, 
                     'page'        => $page, 
                     'author'      => $entry['author'],
                     'create_date' => $entry['create_date'],
                     'modify_date' => time());
       
       $this->db->where('id', $id);
       $this->db->update($this->tableName, $row);
    }
    // }}}
    // {{{ deletePage
    public function deletePage($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));
    }
    // }}}
    // {{{ & getPage
    public function & getPage($id)
    {   
        $this->db->select('id, subject, page, author, create_date');
        $this->db->from($this->tableName);
        $this->db->where('id', $id);
        
        $query = $this->db->get();
        $row = (Array) $query->current();
        
        return $row;
    }
    // }}}
    // {{{ & getPages
    public function & getPages()
    {
        $this->db->select('id, subject, author, create_date, modify_date');
        $this->db->orderby('create_date', 'desc');
        $query = $this->db->get($this->tableName);

        $retval = $query->result(False);
        return $retval;
    }
    // }}}
    // {{{ getDate
    public function getDate($row)
    {
        return date('Y-m-d H:i:s', $row['create_date']);
    }
    // }}}
    // {{{ getModifyDate
    public function getModifyDate($row)
    {
        return date('Y-m-d H:i:s', $row['modify_date']);
    }
    // }}}
    // {{{ checkID
    public function checkID($id)
    {
        $this->db->select('id');
        $query = $this->db->getwhere($this->tableName, Array('id' => $id)); 
        return (boolean)$query->num_rows();
    }
    // }}}
}

?>
