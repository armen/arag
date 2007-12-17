<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Comment_Model extends Model 
{
    // {{{ Properties
    
    private $tableName;

    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Set the table name
        $this->tableName = 'comments';
    }
    // }}}
    // {{{ createComment
    public function createComment($moduleName, $author, $comment, $parentId = 0, $name = Null, $email = Null, $homepage = Null)
    {
        $row = Array('module_name' => $moduleName, 
                     'comment'     => $comment,
                     'parent_id'   => $parentId,
                     'author'      => $author,
                     'homepage'    => $homepage,
                     'name'        => $name,
                     'email'       => $email,                     
                     'create_date' => time(),
                     'created_by'  => $author,
                     'modify_date' => 0,
                     'modified_by' => Null);

        $this->db->insert($this->tableName, $row);
    }
    // }}}
    // {{{ editComment
    public function editComment($id, $author, $comment, $name = False, $email = False, $homepage = False, $parentId = False)
    {
        $comment = $this->getComment($id);

        $row = Array('module_name' => $comment['module_name'], 
                     'comment'     => $comment,
                     'parent_id'   => ($parentId === False) ? $comment['parent_id'] : $parentId,
                     'author'      => $comment['author'],
                     'homepage'    => ($homepage === False) ? $comment['homepage'] : $homepage,
                     'name'        => ($name === False) ? $comment['name'] : $name,
                     'email'       => ($email === False) ? $comment['email'] : $email,                     
                     'create_date' => $comment['create_date'],
                     'created_by'  => $comment['created_by'],
                     'modify_date' => time(),
                     'modified_by' => $author);

       $this->db->where('id', $id);
       $this->db->update($this->tableName, $row);
    }
    // }}}
    // {{{ deleteComment
    public function deleteComment($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));
    }
    // }}}
    // {{{ & getComment
    public function & getComment($id)
    {
        $this->db->select('module_name, comment, parent_id, author, homepage, name, email,'.
                          'create_date, created_by, modify_date, modified_by');

        $query = $this->db->getwhere($this->tableName, Array('id' => $id));

        $retval = (array) $query->current();
        return $retval;
    }
    // }}}
    // {{{ & getComments
    public function & getEntries($moduleName)
    {
        $this->db->select('id, comment, parent_id, author, homepage, name, email,'.
                          'create_date, created_by, modify_date, modified_by');
        $this->db->orderby('create_date', 'desc');
        
        $query = $this->db->getwhere($this->tableName, Array('module_name' => $moduleName));

        $retval = $query->result(False);
        return $retval;
    }
    // }}}

    // {{{ List callbacks
    // {{{ getDate
    public function getDate($row)
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

?>
