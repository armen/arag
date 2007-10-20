<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author:   Sasan Rose <sasan.rose@gmail.com>                             |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class UserManagementModel extends Model 
{
    // {{{ Properties
    
    var $tableName;

    // }}}
    // {{{ Constructor
    function UserManagementModel()
    {
        parent::Model();

        // Connecting to the database
        $this->load->database();

        // set tables' names
        $this->tableNameApp = "user_applications";
    }
    // }}}
    // {{{ createPage
    function createPage($author, $subject, $page)
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
    function editPage($id, $subject, $page)
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
    function deletePage($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));
    }
    // }}}
    // {{{ & getPage
    function & getPage($id)
    {   
        $this->db->select('id, subject, page, author, create_date');
        $this->db->from($this->tableName);
        $this->db->where('id', $id);
        
        $query = $this->db->get();
        $row = $query->row_array();
        
        return $row;
    }
    // }}}
    // {{{ getModifyDate
    function getModifyDate($row)
    {
        return date('Y-m-d H:i:s', $row['modify_date']);
    }
    // }}}
    // {{{ checkID
    function checkID($id)
    {
        $this->db->select('id');
        $this->db->from($this->tableName);
        $this->db->where('id', $id);
        
        $query = $this->db->get();
        $row = $query->row_array();
        
        return $this->db->affected_rows();
    }
    // }}}
    // {{{ getApp
    function getApp($name)
    {
        $this->db->select('name, default_group, create_date');
        $this->db->from($this->tableNameApp);
        
        if ($name != "") {
            $this->db->like("name", $name);
        }
        
        $this->db->orderby('name', 'asc');

        $query = $this->db->get();
        
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
