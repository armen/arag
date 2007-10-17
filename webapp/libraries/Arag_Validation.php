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

        if ($this->CI->uri->router->fetch_request_method() == 'read') {
            // Map request to POST
            $this->_map_request();
        }        

        if (!$retval) {
        
            // Populate all fileds to smarty templates
            if (!$data && !$field && $this->CI->config->item('Arag_smarty_integration') == True) {
                
                $fields        = array_keys($this->_fields);
                $escape_filter = in_array('arag_escape', $this->CI->config->item('Arag_smarty_pre_filters'));
            
                foreach($fields as $field) {

                    if ($escape_filter) {
                        // filed will be escaped at template automatically so escape is unnecessary
                        if (isset($_POST[$field])) {
                            $this->CI->smarty->assign($field, $_POST[$field]);
                        }
                    } else {
                        // $this->$field is secured in parent::set_fields with prep_for_form function
                        $this->CI->smarty->assign($field, $this->$field);
                    }
                }
            }

        } else {
            return $retval;
        }       
    }
    // }}}
    // {{{ _map_request
    function _map_request()
    {
        if (is_array($this->_fields)) {

            $fields        = array_keys($this->_fields);
            $query_strings = $this->CI->config->item('enable_query_strings');

            foreach ($fields as $field) {
                if (is_numeric($field)) {
                    // We have 3/4 leader segments /<module_name>/[<directory_name>/]<class_name>/<method_name>/                
                    $_POST[$field] = $this->CI->uri->rsegment($field + (($this->CI->uri->router->fetch_directory() == '') ? 3 : 4));
                } elseif ($query_strings) {
                    $_POST[$field] = $_GET[$field];
                }
            }
        }
    }
    // }}}
}

?>
