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
 * Input Class
 *
 * @package     Arag
 * @subpackage  Libraries
 * @category    Input
 * @author      Armen Baghumian
 */
class Arag_Input extends CI_Input {

    // {{{ post
    /**
     * Fetch an item from the POST array
     *
     * @access    public
     * @param    string
     * @param    bool
     * @return    string
     */
    function post($index = '', $xss_clean = False, $default_value = False)
    {
        $result = parent::post($index, $xss_clean);

        return ($result === False) ? $default_value : $result;
    }
    // }}}
}

?>
