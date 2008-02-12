<?php
/**
 * Arag
 *
 * @package      Arag
 * @author       Armen Baghumian
 * @since        Version 0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Component Class
 *
 * Components base class
 *
 * @package     Arag
 * @subpackage  Libraries
 * @author      Armen Baghumian
 * @category    WorkFlow
 */
abstract class WorkFlow {

    // {{{ route
    abstract public static function route();
    // }}}
    // {{{ resume
    abstract public static function resume();
    // }}}    
}

?>
