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
 * Output Class
 *
 * Responsible for sending final output to browser
 *
 * @package		Arag
 * @subpackage	Libraries
 * @author		ArmenBaghumian
 * @category	Output 
 */
class Arag_Output extends CI_Output {

    // {{{ Properties
    
    var $slots     = Array();
    var $decorator = Null;

    // }}}
	// {{{ set_output
    /**
	 * Set Output
	 *
	 * Sets the output string
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	function set_output($output, $slot_name = Null, $append = True)
	{
        if ($this->get_decorator()) {

            global $RTR;
            
            $this->slots[$slot_name] = ($append && isset($this->slots[$slot_name]))? 
                                        $this->slots[$slot_name] . $output : 
                                        $output;

            // We dont have output yet! :)
            $this->final_output = Null;
        
        } else {

            $this->final_output = ($append) ? $this->final_output . $output : $this->final_output;
        }
	}
    // }}}
    // {{{ set_decorator
    function set_decorator($decorator)
    {
        $this->decorator = $decorator;
    }
    // }}}
    // {{{ get_decorator
    function get_decorator()
    {
        return $this->decorator;
    }
    // }}}    
    // {{{ _display
    function _display($output = '')
    {
        if ($decorator = $this->get_decorator()) {

            $CI =& get_instance();

            if (!isset($this->slots['content'])) {
                // If The main method has not any view the content will be set to Null
                $this->slots['content'] = Null;
            }

            $CI->load->vars(Array('_slots' => $this->slots));
            $this->final_output = $CI->load->view($decorator, Array(), True);
        }

        parent::_display($output);
    }
    // }}}
}

?>
