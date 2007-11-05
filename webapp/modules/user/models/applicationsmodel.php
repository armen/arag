<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author:   Sasan Rose <sasan.rose@gmail.com>                             |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Applications_Model extends Model 
{
    // {{{ Properties
    
    var $tableNameApps;
    var $tableNameGroups;
    var $tableNameUsers;

    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Connecting to the database
// $this->load->database();

        // set tables' names
        $this->tableNameApps   = "user_applications";
        $this->tableNameGroups = "user_groups";
        $this->tableNameUsers  = "user_users";
    }
    // }}}
    // {{{ getApps
    function getApps($name)
    {
        $this->db->select('name, default_group, create_date');
        $this->db->from($this->tableNameApps);
        
        if ($name != "") {
            $this->db->like("name", $name);
        }
        
        $this->db->orderby('name', 'asc');

        $query = $this->db->get();
        
        $retval = $query->result(False);
        return $retval;
    }
    // }}}
    // {{{ getDate
    function getDate($row)
    {
        return date('Y-m-d H:i:s', $row['create_date']);
    }
    // }}}
    // {{{ getModifyDate
    function getModifyDate($row)
    {
        return date('Y-m-d H:i:s', $row['modify_date']);
    }
    // }}}
    // {{{ hasApp
    function hasApp($name)
    {
        $this->db->select('name');
        $query = $this->db->getwhere($this->tableNameApps, Array('name' => $name)); 
        return (boolean)$query->num_rows();
    }
    // }}}
}
?>
