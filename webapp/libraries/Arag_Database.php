<?php
/**
 *
 * @package    Arag
 * @author     Armen Baghumian
 * @since      Version 0.3
 * @filesource
 * $Id$
 */

/**
 * Database Class
 *
 * @category    Libraries
 *
 */
class Database extends Database_Core {

    // {{{ reset_select_only
    public function reset_select_only()
    {
        $this->select = array();
    }
    // }}}
    // {{{ get_sql
    public function get_sql()
    {
        return $this->driver->compile_select(get_object_vars($this));
    }
    // }}}
    // {{{ is_select_dependent
    public function is_select_dependent()
    {
        return count($this->groupby) || count($this->having) || $this->distinct;
    }
    // }}}
}
