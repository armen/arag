<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Arag
 *
 * @package      Arag
 * @author       Armen Baghumian
 * @since        Version 0.1
 * @filesource
 * $Id$
 */

// ------------------------------------------------------------------------

/**
 * Controller Class
 *
 * @category    Libraries
 *
 */
class Controller extends Controller_Core {

    // {{{ Properties

    public $layout = Null;

    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();
    
        if (Config::item('smarty.integration') == True) {
            $this->load->library('Arag_Smarty');
        }

        if ($this->layout == Null) {
            $this->layout = (strpos(Router::$directory, 'backend') !== False || Router::$controller === 'backend') 
                          ? 'arag_templates/backend_layout' 
                          : 'arag_templates/frontend_layout';
        }

        $this->layout = new View($this->layout);

        $this->layout->firstname = $this->session->get('name');
        $this->layout->surname   = $this->session->get('lastname');
        $this->layout->auth      = $this->session->get('authenticated');

        // Set default page_title
        $this->layout->page_title = 'Arag';
        
        Event::add('system.post_controller', array($this, '_display'));
    }
    // }}}
    // {{{ smarty_include_view
    /**
     * Fetchs a smarty template within the controller scope
     *
     * @access public
     * @param  string
     * @param  array
     * @return string
     */
    public function smarty_include_view($template, $vars)
    {
        if ($template == '')
            return;
        
        // Assign variables to the template
        if (is_array($vars) && count($vars) > 0) {
            foreach ($vars as $key => $val) {
                $this->Arag_Smarty->assign($key, $val);
            }
        }

        // Send Kohana::instance and base url to all templates
        $this->Arag_Smarty->assign('this', $this);

        $base_url = Config::item('sites/'.APPNAME.'.core.parent_base_url', False, False) ? 
                    Config::item('sites/'.APPNAME.'.core.parent_base_url') : 
                    url::base();
        $this->Arag_Smarty->assign('arag_base_url', $base_url);

        // Fetch the output
        $output = $this->Arag_Smarty->fetch($template);

        return $output;
    }
    // }}}
    // {{{ _remap
    function _remap($method, $arguments)
    {
        // Set the method and arguments of Router
        Router::$method    = $method;
        Router::$arguments = $arguments;

        $validated = True;

        if (file_exists($validator_file = str_replace('controllers', 'validator', Router::$directory.Router::$controller.EXT))) {
            
            include($validator_file);

            $_validator = isset($validator[$method][Router::$request_method]['rules']) ? 
                          $validator[$method][Router::$request_method]['rules'] : 
                          (isset($validator[$method]['rules']) ? $validator[$method]['rules'] : Null);
            
            if (is_array($_validator)) {

                if (Router::$request_method == 'read') {
                    // GET/Segments validation
                    $this->load->library('validation', $arguments);
                } else {
                    $this->load->library('validation');
                }

                $this->validation->set_rules($_validator);

                // XXX: this should be $validator not $_validator
                if (isset($validator['error_messages']) && is_array($validator['error_messages'])) {
                    $this->validation->set_message($validator['error_messages']);
                }

                $validated = $this->validation->run();

                if (!$validated && Config::item('smarty.integration') == True) {
                    // An error occured so repopulate it to smarty templates
                    $data = $this->validation->data_array;
                    foreach ($data as $field => $value) {
                        $this->Arag_Smarty->assign($field, $value);
                    }
                }
            }

            // Cleanup
            unset($validator);
            unset($_validator);
        }

        $alt_method = $method . '_' . Router::$request_method;
        $alt_method = ($validated) ? $alt_method : $alt_method . '_error';
        $method     = ($validated) ? $method : $method . '_error';

        // Is method exists?
        $method = method_exists($this, $method) ? $method : (method_exists($this, $alt_method) ? $alt_method : False);
        
        if ($method == False) {
            Event::run('system.404');
        }

        if (empty($arguments)) {
            // Call the controller method with no arguments
            $this->$method();

        } else {

            // Manually call the request method for up to 4 arguments. Why? Because
            // call_user_func_array is ~3 times slower than direct method calls.
            switch(count($arguments))
            {
                case 1:
                    $this->$method($arguments[0]);
                break;
                case 2:
                    $this->$method($arguments[0], $arguments[1]);
                break;
                case 3:
                    $this->$method($arguments[0], $arguments[1], $arguments[2]);
                break;
                case 4:
                    $this->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                break;
                default:
                    // Resort to using call_user_func_array for many segments
                    call_user_func_array(array($this, $method), $arguments);
                break;
            }
        }
    }
    // }}}
    // {{{ _invalid_request
    function _invalid_request($uri = Null)
    {
        $this->session->set('_invalid_request_uri', $uri);

        url::redirect('invalid_request');
    }
    // }}}    
    // {{{ _display
    public function _display()
    {
        if ($this->layout instanceof View) {
            $this->layout->render(True);
        }
    }
    // }}}
}

?>