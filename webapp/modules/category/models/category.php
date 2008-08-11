<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Category_Model extends Model
{
    // {{{ Properties

    private $tableName;

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Set the table name
        $this->tableName = 'categories';
    }
    // }}}
    // {{{ getCategories
    public function getCategories($module, $parent_id, $orderby = NULL, $order = 'ASC', $limit = NULL)
    {
        $this->db->select('id, parent_id, name, module_name');
        $this->db->where('parent_id', $parent_id);

        if ($orderby != NULL) {
            $this->db->orderby($orderby, $order);
        }

        if ($limit != NULL) {
            $this->db->limit($limit);
        }

        return $this->db->getwhere($this->tableName, array('parent_id' => $parent_id, 'module_name' => $module))->result(false);

    }
    // }}}
    // {{{ getCatNumbers
    public function getCatNumbers($module, $parent_id)
    {
        $result = $this->db->select('count(id) as count')->getwhere($this->tableName, Array('parent_id' => $parent_id, 'module_name' => $module))
                -> current();
        return $result->count;
    }
    // }}}
    // {{{ hasCategory
    public function hasCategory($id)
    {
        $result = $this->db->select('count(id) as count')->getwhere($this->tableName, Array('id' => $id))->current();
        return (boolean) $result->count;
    }
    // }}}
    // {{{ getCategory
    public function getCategory($id)
    {
        $this->db->select('id, parent_id, name, module_name');

        return (array) $this->db->getwhere($this->tableName, array('id' => $id))->current();
    }
    // }}}
}
?>
