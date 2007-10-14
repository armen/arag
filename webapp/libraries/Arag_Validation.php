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
 * @category    Validation
 * @author      Armen Baghumian
 */

class Arag_Validation extends CI_Validation {
    
    // {{{ set_fields    
    function set_fields($data = '', $field = '')
    {
        $retval = parent::set_fields($data, $field);

        $CI =& get_instance();        

        if (!$retval) {
        
            // Populate all fileds to smarty templates
            if (!$data && !$field && $CI->config->item('Arag_smarty_integriation') == True) {
                
                $fields        = array_keys($this->_fields);
                $escape_filter = in_array('arag_escape', $CI->config->item('Arag_smarty_pre_filters'));
            
                foreach($fields as $field) {

                    if ($escape_filter) {
                        // filed will be escaped at template automatically so escape is unnecessary
                        if (isset($_POST[$field])) {
                            $CI->smarty->assign($field, $_POST[$field]);
                        }
                    } else {
                        // $this->$field is secured in parent::set_fields with prep_for_form function
                        $CI->smarty->assign($field, $this->$field);
                    }
                }
            }

        } else {
            return $retval;
        }       
    }
}

?>
