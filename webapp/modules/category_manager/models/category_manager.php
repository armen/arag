<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Peyman Karimi <peykar@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Category_Manager_Model extends Model
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Set table name
        $this->tableNameCategories = 'category_manager_categories';
        $this->tableNameRecords    = 'category_manager_records';
    }
    // }}}
    // {{{ getCategories
    public function getCategories($parent = null, $module = null, $appname = null, $withChilds = false, $flatten = false)
    {
        $this->db->select('id' , 'name', 'label', 'application_name', 'module', 'parent')->from($this->tableNameCategories);

        if ($parent) {
            $this->db->where('parent', $parent);
        } else {
            $this->db->where('parent IS NULL');
        }

        $appname and $this->db->in('application_name', array($appname, '__ALL__'));
        $module and $this->db->where('module', $module);

        $result = $this->db->get()->result_array(false);

        if ($withChilds) {
            foreach($result as &$category) {
                $category['childs'] = $this->getCategories($category['id'], null, null, true);
            }
        }

        return $flatten ? $this->_flattenResult($result) : $result;
    }
    // }}}
    // {{{ _flattenCategoryTree
    private function _flattenResult($categories, $level = 0)
    {
        $result = array();

        foreach($categories as $category){
            $result[] = array_merge($category, array('level' => $level));
            $result   = array_merge($result, $this->_flattenResult($category['childs'], $level+1));
        }

        return $result;
    }
    // }}}
    // {{{getPath
    public function getPath($id)
    {
        $path = array($id);
        $parent = $this->getParent($id);
        while($parent) {
            $path[] = $parent;
            $parent = $this->getParent($parent);
        }
        return array_reverse($path);
    }
    // }}}
    // {{{ getParent
    public function getParent($id)
    {
        $category = $this->getCategory($id);
        if ($category) {
            return $category['parent'];
        }
    }
    // }}}
    // {{{ getCategory
    public function getCategory($id = null, $name = null, $label = null, $module = null, $appname = null, $parent = null)
    {
        $this->db->select('id' , 'name', 'label', 'application_name', 'module', 'parent')->from($this->tableNameCategories);
        $id      and $this->db->where('id', $id);
        $name    and $this->db->where('name', $name);
        $label   and $this->db->where('label', $label);
        $appname and $this->db->where('application_name', $appname);
        $module  and $this->db->where('module', $module);
        $parent  and $this->db->where('parent', $parent);

        return $this->db->get()->result(False)->current();
    }
    // }}}
    // {{{ createCategory
    function createCategory($name, $label, $module, $appname, $parent = null)
    {
        $row = array('name'             => $name,
                     'label'            => $label,
                     'application_name' => $appname,
                     'module'           => $module,
                     'parent'           => $parent);

        return $this->db->insert($this->tableNameCategories, $row)->insert_id();
    }
    // }}}
    // {{{ editCategory
    function editCategory($id, $name, $label, $module, $appname, $parent = null)
    {
        return $this->db->where(array('id' => $id, 'module' => $module, 'application_name' => $appname, 'parent' => $parent))
                    ->update($this->tableNameCategories, array('name' => $name, 'label' => $label));
    }
    // }}}
    // {{{ deleteCategory
    function deleteCategory($id, $appname)
    {
        foreach($this->getCategories($id) as $subCategory) {
            $this->deleteCategory($subCategory['id']);
        }
        $this->db->from($this->tableNameRecords)->where(array('category_id' => $id, 'application_name' => $appname))->delete();
        $this->db->from($this->tableNameCategories)->where(array('id' => $id, 'application_name' => $appname))->delete();
    }
    // }}}
    // {{{ addToCategory
    function addToCategory($category_id, $entity_id, $appname)
    {
        $row = array('category_id'      => $category_id,
                     'entity_id'        => $entity_id,
                     'application_name' => $appname);

        return $this->db->insert($this->tableNameRecords, $row)->insert_id();
    }
    // }}}
    // {{{ deleteCategoryRecord
    function deleteCategoryRecord($id, $appname)
    {
        $this->db->from($this->tableNameRecords)->where(array('id' => $id, 'application_name' => $appname))->delete();
    }
    // }}}
}
