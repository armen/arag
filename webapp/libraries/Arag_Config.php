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
    public static function set($name, $value, $namespace = Null, $appname = APPNAME)
    {
        $db = new Database();

        if ($namespace == Null) {
            $namespace = ($appname === True) ? Router::$module : $appname.'::'.Router::$module;
        } else {
            $namespace = ($appname === True) ? $namespace : $appname.'::'.$namespace;
        }

        $db->select('value');
        $query = $db->getwhere(self::$tableName, Array('name' => $name, 'namespace' => $namespace));

        // Serialize value
        if (get_magic_quotes_gpc() == True) {
            $value = serialize($value);
        } else {
            $value = addslashes(serialize($value));
        }

        if (count($query) == 0){
            $db->insert(self::$tableName, Array('name' => $name, 'namespace' => $namespace, 'value' => $value));
        } else {
            $db->update(self::$tableName, Array('name' => $name, 'namespace' => $namespace, 'value' => $value),
                                          Array('name' => $name, 'namespace' => $namespace));
        }
        static $cache;
        if (!isset($cache)) {
            $cache = new Cache;
        }
        $cache->delete($name.'_'.$namespace);
    }
    // }}}
    // {{{ & get
    public static function & get($name, $default = Null, $namespace = Null, $try_kohana_config_first = False, $appname = APPNAME)
    {
        if ($namespace == Null) {
            $namespace = ($appname === True) ? Router::$module : $appname.'::'.Router::$module;
        } else {
            $namespace = ($appname === True) ? $namespace : $appname.'::'.$namespace;
        }

        static $cache;
        if (!isset($cache)) {
            $cache = new Cache;
        }

        $id = $name.'_'.$namespace;

        $cached = $cache->get($id);
        if ($cached) {
            return $cached;
        }

        if ($try_kohana_config_first) {

            // Save old include paths
            $old_include_paths = Kohana::include_paths();

            // Change include_once to module path
            Kohana::config_set('core.modules', array_unique(array_merge($old_include_paths, Array(MODPATH.$namespace))));

            $result = Kohana::config($name);
        }

        if (empty($result)) {
            static $db;
            if (!isset($db)) {
                $db     = new Database();
            }
            $result = $default;

            $db->select('value');
            $query = $db->getwhere(self::$tableName, Array('name' => $name, 'namespace' => $namespace));

            if (count($query) > 0) {
                if (get_magic_quotes_gpc() == True) {
                    $result = unserialize($query->current()->value);
                } else {
                    $result = unserialize(stripslashes($query->current()->value));
                }
            }
        }

        $cache->set($id, $result);
        return $result;
    }
    // }}}
    // {{{ & get_namespace
    public static function & get_namespace($namespace = Null, $appname = APPNAME)
    {
        $db     = new Database();
        $result = Array();

        if ($namespace == Null) {
            $namespace = ($appname === True) ? Router::$module : $appname.'::'.Router::$module;
        } else {
            $namespace = ($appname === True) ? $namespace : $appname.'::'.$namespace;
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
    // {{{ delete
    public function delete($name, $namespace = Null, $appname = APPNAME)
    {
        if (!$namespace) {
            $namespace = ($appname === True) ? Router::$module : $appname.'::'.Router::$module;
        }

         $db = New Database();
         return $db->delete(self::$tableName, Array('name' => $name, 'namespace' => $namespace));
    }
    // }}}
}
