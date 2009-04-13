<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Calendar_Model extends Model
{
    // {{{ Properties
    public $tableName = 'calendar_holidays';
    // }}}
 
    // {{{ add
    public function add($date, $description)
    {
        return $this->db->insert($this->tableName, array('date' => $date, 'description' => $description));
    }
    // }}}
    // {{{ edit
    public function edit($id, $date, $description)
    {
        return $this->db->where('id', $id)->update($this->tableName, array('date' => $date, 'description' => $description));
    }
    // }}}
    // {{{ getAll
    public function getAll()
    {
        return $this->db->select('id', 'date', 'description')->from($this->tableName)->get()->result(False);
    }
    // }}}
    // {{{ get
    public function get($id)
    {
        return $this->db->select('id', 'date', 'description')->from($this->tableName)->where('id', $id)->get()->result(False)->current();
    }
    // }}}
    // {{{ delete
    public function delete($id)
    {
        return $this->db->where('id', $id)->from($this->tableName)->delete();
    }
    // }}}
}

?>
