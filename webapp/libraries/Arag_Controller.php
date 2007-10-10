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
 * CodeIgniter Application Controller Class
 *
 * This class object is the super class the every library in
 * CodeIgniter will be assigned to.
 *
 * @package     Arag
 * @subpackage  Libraries
 * @category    Libraries
 * @author      Armen Baghumian
 */
class Arag_Controller extends Controller {

    // {{{ _ci_initialize()
    function _ci_initialize()
    {
        parent::_ci_initialize();

        if ($this->config->item('Arag_smarty_integriation') == True) {
            $this->load->library('Arag_Smarty');
        }

        // XXX: better to move to autoload file?
        $this->lang->load();        
    }
    // }}}
}

?>
