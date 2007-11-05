<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Privileges_Model extends Model 
{
    // {{{ Properties
    
    var $tableNamePrivileges;
    var $tableNameGroups;
    private $session;

    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // set tables' names
        $this->tableNamePrivileges = "user_privileges";        
        $this->tableNameGroups     = "user_groups";

        $this->session = Kohana::instance()->session;
    }
    // }}}
    // {{{ getFilteredPrivileges
    function getFilteredPrivileges($appname, $parentId)
    {
        $controller = Kohana::instance();
        $controller->load->model('Filters', 'Filters', 'user');

        $filters = $controller->Filters->getPrivilegeFilters($appname);
        
        $this->db->select('*')->from($this->tableNamePrivileges);

        if ($parentId != NULL) {
            $this->db->where(array('parent_id' => $parentId));
        }

        $privileges = $this->db->get()->result_array(False);
        
        if (is_array($filters)) {

            foreach ($filters as $filter) {
            
                // It contains four section which every section separated with a /.
                // It should contain at least two sections. Each section contains * 
                // (except first section and last when we have 4 or 3 sections) or 
                // lower case character(s)
                if ($filter === '*' || preg_match('/^([a-z_]+)((\/[a-z_]+){0,2}(\/\*))|((\/[a-z_]+){2,3})$/', $filter)) {

                    $filter = '|^'.str_replace('*', '.*', $filter).'$|';

                    foreach ($privileges as $key => $row) {

                        if (preg_match($filter, $row['privilege'])) {
                            unset($privileges[$key]);
                        }
                    }                        
                }
            }
        }

        return $privileges;
    }
    // }}}
    // {{{ getLabel
    function getLabel($id)
    {
        $this->db->select('*');
        $this->db->from($this->tableNamePrivileges);
        
        return $this->db->where(array('id' => $id))->get()->current();
    }
    // }}}
    // {{{ addLabel
    function addLabel($label, $parentid, $privilege = NULL)
    {
        $modified_by = $this->session->get('username');
        $created_by  = $this->session->get('username');

        $rows = array ('label'       => $label,
                       'parent_id'   => $parentid,
                       'privilege'   => trim(strtolower($privilege), '/'),
                       'modified_by' => $modified_by,
                       'created_by'  => $created_by,
                       'modify_date' => time(),
                       'create_date' => time());
        
        $this->db->insert($this->tableNamePrivileges, $rows);

        if ($parentid != "0") {
            $this->db->where('id', $parentid);
            $this->db->update($this->tableNamePrivileges, array('modified_by' => $modified_by, 'modify_date' => time()));
        }
    }
    // }}}
    // {{{ editLabel
    function editLabel($label, $id, $privilege = NULL)
    {
        $modified_by = $this->session->get('username');
        $row         = $this->getLabel($id);
        
        $rows = array ('label'       => $label,
                       'privilege'   => trim(strtolower($privilege), '/'),
                       'modified_by' => $modified_by,
                       'modify_date' => time());
        
        $this->db->where('id', $id);
        $this->db->update($this->tableNamePrivileges, $rows);

        if ($id != 0) {
            $this->db->where('id', $row->parent_id);
            $this->db->update($this->tableNamePrivileges, array('modified_by' => $modified_by, 'modify_date' => time()));
        }
    }
    // }}}
    // {{{ isParent
    function isParent($row)
    {
        if ($row['parent_id'] === "0") {
            return false;
        }
        return true;
    }
    // }}}
    // {{{ hasLabel
    function hasLabel($id)
    {
        $this->db->select('id');
        $query = $this->db->getwhere($this->tableNamePrivileges, Array('id' => $id)); 
        return (boolean)$query->num_rows();
    }
    // }}}
    // {{{ deletePrivileges
    function deletePrivileges($objects)
    {
        foreach ($objects as $object) {
            $label = $this->getLabel($object);    

            if ($label->parent_id === "0") {
                $this->db->delete($this->tableNamePrivileges, array('parent_id' => $object));
            } else {
                $row = array ('modified_by' => $this->session->get('username'),
                              'modify_date' => time());
                $this->db->where('id', $label->parent_id);
                $this->db->update($this->tableNamePrivileges, $row);
            }

            $this->db->delete($this->tableNamePrivileges, array('id' => $object));
        }
    }
    // }}}
    // {{{ getPrivileges
    function getPrivileges($id, $flag = false)
    {
        $this->db->select('privileges');
        $this->db->from($this->tableNameGroups);
        $this->db->where('id', $id);

        $row  = $this->db->get()->result(False);

        if ($row[0]['privileges'] == NULL) {
            return $filter = array();
        }
        
        $rows = unserialize($row[0]['privileges']);

        if ($flag) {
            return $rows;
        }
        
        $privileges = array();

        foreach ($rows as $key => $privilege) {
            
            $privileges[$key]['privilege'] = $privilege;
            $privileges[$key]['id']        = $key;
            $privileges[$key]['label']     = Null;

            if ($labels = $this->getLabelByPrivilege($privilege)) {
                foreach ($labels as $keys => $row) {
                    $alllabels[$keys] = $row->label;
                }
                $privileges[$key]['label'] = implode(", ", $alllabels);               
            }
        }

        return $privileges;
    }
    // }}}
    // {{{ getLabelByPrivilege
    function getLabelByPrivilege($privilege)
    {
        $this->db->select('label');
        $query = $this->db->getwhere($this->tableNamePrivileges, array('privilege' => $privilege));
        if ((boolean) $query->num_rows()) {
            return $query->result();
        }
    }
    // }}}
    // {{{ getAppPrivileges
    function getAppPrivileges($appname)
    {
        $rows = $this->getFilteredPrivileges($appname, 0);
        
        $subpris = array();
        
        foreach ($rows as $row) {
            $subpris[$row['id']] = $this->getFilteredPrivileges($appname, $row['id']);
        }

        return $subpris;
    }
    // }}}
    // {{{ getSelectedPrivileges
    function getSelectedPrivileges($subpris, $allselected)
    {
        foreach ($subpris as $id => $privilege) {
            foreach ($privilege as $key => $subprivilege) {
                if ($allselected != NULL) {
                    foreach ($allselected as $selected) {
                        if ($subprivilege['privilege'] == $selected) {
                            $subpris[$id][$key]['selected'] = true;
                            break;
                        } else {
                            $subpris[$id][$key]['selected'] = false;
                        }
                    }
                } else {
                    $subpris[$id][$key]['selected'] = false;
                }
            }
        }

        return $subpris;
    }
    // }}}
    // {{{ getSelectedParents
    function getSelectedParents($selected, $parents)
    {
        foreach ($parents as $key => $parent) {
            foreach ($selected[$parent['id']] as $select) {
                if ($select['selected']) {
                    $parents[$key]['selected'] = true;
                } else {
                    $parents[$key]['selected'] = false;
                    break;
                }
            }
        }

        return $parents;
    }
    // }}}
    // {{{ editPrivileges
    function editPrivileges($ids, $groupid, $appname)
    {
        $privileges = array();
        
        foreach ($ids as $id) {
            $label = $this->getLabel($id);
            if ($label->parent_id === "0") {
                $subpris = $this->getFilteredPrivileges($appname, $id);
                foreach ($subpris as $subpri) {
                    $privileges[] = $subpri['privilege'];
                }
            } else {
                $privileges[] = $label->privilege;
            }
        }

        $privileges = serialize(array_unique($privileges));

        $this->db->where('id', $groupid);
        $this->db->update($this->tableNameGroups, array('privileges' => $privileges));
    }
    // }}}
}

?>
