<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Arag_Config_Core
{    
    // {{{ Properties

    private static $tableName = 'core_configuration';

    // }}}
    // {{{ set
    public static function set($name, $value, $namespace = Null)
    {
        $db = new Database();

        if ($namespace == Null) {
            $namespace = Router::$module;
        }

        $db->select('value');
        $query = $db->getwhere(self::$tableName, Array('name' => $name, 'namespace' => $namespace));

        // Serialize value
        if (get_magic_quotes_gpc() == True) {
            $value = serialize($value);
        } else {
            $value = serialize(addslashes($value));
        }        

        if (count($query) == 0){
            $db->insert(self::$tableName, Array('name' => $name, 'namespace' => $namespace, 'value' => $value));
        } else {
            $db->update(self::$tableName, Array('name' => $name, 'namespace' => $namespace, 'value' => $value), 
                                          Array('name' => $name, 'namespace' => $namespace));
        }
    }        
    // }}}    
    // {{{ & get
    public static function & get($name, $default = Null, $namespace = Null)
    {
        $db     = new Database();    
        $result = $default;

        if ($namespace == Null) {
            $namespace = Router::$module;
        }
        
        $db->select('value');
        $query = $db->getwhere(self::$tableName, Array('name' => $name, 'namespace' => $namespace));

        if (count($query) > 0) {
            if (get_magic_quotes_gpc() == True) {
                $result = unserialize(stripslashes($query->current()->value));
            } else {
                $result = unserialize($query->current()->value);
            }
        }

        return $result;
    }
    // }}}
    // {{{ & get_namespace
    public static function & get_namespace($namespace = Null)
    {
        $db     = new Database();    
        $result = Array();

        if ($namespace == Null) {
            $namespace = Router::$module;
        }
        
        $db->select('name, value');
        $query = $db->getwhere(self::$tableName, Array('namespace' => $namespace));

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
