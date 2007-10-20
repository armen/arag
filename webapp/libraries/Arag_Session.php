<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * Session Class
 *
 * @package     Arag
 * @subpackage  Libraries
 * @category    Session
 * @author      Armen Baghumian
 */
class Arag_Session extends CI_Session {

    // {{{ userdata
    /**
     * Fetch a specific item form  the session array
     *
     * @access    public
     * @param     string
     * @return    string
     */        
    function userdata($item, $default_value = False)
    {
        $result = parent::userdata($item);

        return ($result === False) ? $default_value : $result;
    }
    // }}}
    // {{{ has_userdate
    function has_userdata($item)
    {
        return isset($this->userdata[$item]) && $this->userdata[$item] == Null;
    }
    // }}}    
}

?>
