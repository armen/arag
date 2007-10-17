<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Configuration extends Model 
{    
    // {{{ Properties

    var $tableName = 'core_configuration';

    // }}}    
    // {{{ setVar
    function setVar($name, $value, $namespace = Null)
    {
        if ($namespace == Null) {
            $namespace = $this->uri->router->fetch_module();
        }

        $this->db->select('value');
        $query = $this->db->getwhere($this->tableName, Array('name' => $name, 'namespace' => $namespace));

        // Serialize value
        if (get_magic_quotes_gpc() == True) {
            $value = serialize($value);
        } else {
            $value = serialize(addslashes($value));
        }        

        if ($query->num_rows() == 0){
            $this->db->insert($this->tableName, Array('name' => $name, 'namespace' => $namespace, 'value' => $value));
        } else {
            $this->db->update($this->tableName, Array('name' => $name, 'namespace' => $namespace, 'value' => $value), 
                                                Array('name' => $name, 'namespace' => $namespace));
        }
    }        
    // }}}    
    // {{{ & getVar
    function & getVar($name, $defaultValue = Null, $namespace = Null)
    {
        $result = $defaultValue;

        if ($namespace == Null) {
            $namespace = $this->uri->router->fetch_module();
        }
        
        $this->db->select('value');
        $query = $this->db->getwhere($this->tableName, Array('name' => $name, 'namespace' => $namespace));

        if ($query->num_rows() > 0) {
            if (get_magic_quotes_gpc() == True) {
                $result = unserialize(stripslashes($query->row()->value));
            } else {
                $result = unserialize($query->row()->value);
            }
        }

        return $result;
    }
    // }}}
    // {{{ & getNamespace
    function & getNamespace($namespace = Null)
    {
        $result = Array();

        if ($namespace == Null) {
            $namespace = $this->uri->router->fetch_module();
        }
        
        $this->db->select('name, value');
        $query = $this->db->getwhere($this->tableName, Array('namespace' => $namespace));

        foreach ($query->result() as $row) {
        
           if (get_magic_quotes_gpc() == True) {
               $result[$row->name] = unserialize(stripslashes($row->value));           
           } else {
               $result[$row->name] = unserialize($row->value);           
           }
        }        
        
        return $result;
    }
    // }}}
};

?>
