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
    public function __construct()
    {
        parent::__construct();

        // Set the table name
        $this->tableName = 'comments';
    }
    // }}}
    // {{{ createComment
    public function createComment($moduleName, $referenceId, $author, $comment, $verified = 0, $parentId = 0, $name = Null, $email = Null, $homepage = Null)
    {
        $row = Array('module_name'  => $moduleName, 
                     'reference_id' => $referenceId,
                     'comment'      => $comment,
                     'parent_id'    => $parentId,
                     'homepage'     => $homepage,
                     'name'         => $name,
                     'email'        => $email,
                     'verified'     => $verified,
                     'create_date'  => time(),
                     'created_by'   => $author,
                     'modify_date'  => 0,
                     'modified_by'  => Null);

        $this->db->insert($this->tableName, $row);
    }
    // }}}
    // {{{ editComment
    public function editComment($id, $author, $comment, $verified = False, $name = False, $email = False, $homepage = False, $parentId = False)
    {
        $comment = $this->getComment($id);

        $row = Array('module_name'  => $comment['module_name'], 
                     'reference_id' => $comment['reference_id'],
                     'comment'      => $comment,
                     'parent_id'    => ($parentId === False) ? $comment['parent_id'] : $parentId,
                     'homepage'     => empty($homepage) ? $comment['homepage'] : $homepage,
                     'name'         => empty($homepage) ? $comment['name'] : $name,
                     'email'        => empty($homepage) ? $comment['email'] : $email,                     
                     'verified'     => ($verified === False) ? $comment['verified'] : $verified,
                     'create_date'  => $comment['create_date'],
                     'created_by'   => $comment['created_by'],
                     'modify_date'  => time(),
                     'modified_by'  => $author);

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
        $this->db->select('module_name, reference_id, comment, parent_id, homepage, name, email, verified,'.
                          'create_date, created_by, modify_date, modified_by');

        $query = $this->db->getwhere($this->tableName, Array('id' => $id));

        $retval = (array) $query->current();
        return $retval;
    }
    // }}}
    // {{{ getComments
    public function getComments($moduleName, $referenceId = False, $verified = Null)
    {
        $query = $this->db->select('id, reference_id, comment, parent_id, homepage, name, email, verified,'.
                                   'create_date, created_by, modify_date, modified_by');
        
        if ($referenceId !== False) {
            $query->where('reference_id', $referenceId);
        }

        if ($verified !== Null) {
            $query->where('verified', $verified);
        }
        
        $retval = $query->where('module_name', $moduleName)->orderby('create_date', 'asc')->get($this->tableName)->result();

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

?>
