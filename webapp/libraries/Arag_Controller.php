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

        if ($this->config->item('Arag_smarty_integration') == True) {
            $this->load->library('Arag_Smarty');
        }

        // Load module config file and do not throw error if there was an error
        global $RTR;
        $this->config->load(APPPATH . 'modules/'. $RTR->fetch_module() . '/config/config'.EXT, False, True);

        // XXX: better to move to autoload file?
        $this->lang->load();        
    }
    // }}}
    // {{{ _invalid_request
    function _invalid_request($uri = Null)
    {
        $this->load->helper('url');
        
        // Replace current decorator with global decorator
        $this->output->set_decorator('arag_templates/decorator.tpl');

        $this->load->vars(Array('page_title' => _("Invalid Request!"),
                                '_site_url'  => site_url($uri)));

        $this->load->view('arag_templates/invalid_request.tpl');
    }
    // }}}
}

?>
