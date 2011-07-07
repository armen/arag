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
}
