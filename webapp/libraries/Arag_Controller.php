<?php
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
    public $smarty;
    public $validation;

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        if (Kohana::config('smarty.integration') == True) {
            $this->smarty = new Arag_Smarty;
        }

        $this->validation = Validation::factory();

        if ($this->layout == Null) {
            $theme = Kohana::config('theme.default');

            if (strpos(Router::$controller_path, 'backend') !== False || Router::$controller === 'backend') {
                $this->layout = 'themes/'.$theme.'/backend_layout';
            } else {
                $this->layout = 'themes/'.$theme.'/frontend_layout';
            }
        }

        $this->session = Session::instance();
        $this->layout  = new View($this->layout);

        $this->layout->firstname = $this->session->get('user.name');
        $this->layout->surname   = $this->session->get('user.last_name');
        $this->layout->auth      = $this->session->get('user.authenticated');

        // Set default page_title
        $this->layout->page_title = ucwords(APPNAME);

        // Initialize gettext
        bindtextdomain('messages', MODPATH . Router::$module . '/locale/');
        bind_textdomain_codeset('messages', 'utf8');
        textdomain('messages');

        $locale = current(Kohana::config('locale.language')) . '.utf8';

        putenv('LANG=' . $locale);
        putenv('LANGUAGE=' . $locale);
        setlocale(LC_ALL, $locale);

        Event::add('system.post_controller', array($this, '_display'));
    }
    // }}}
    // {{{ _kohana_load_view
    /**
     * Fetchs a smarty template within the controller scope
     *
     * @access public
     * @param  string
     * @param  array
     * @return string
     */
    public function _kohana_load_view($template, $vars)
    {
        if ($template == '')
            return;

        if (substr(strrchr($template, '.'), 1) === Kohana::config('smarty.templates_ext')) {

            // Assign variables to the template
            if (is_array($vars) && count($vars) > 0) {
                foreach ($vars as $key => $val) {
                    $this->smarty->assign($key, $val);
                }
            }

            // Send Kohana::instance and base url to all templates
            $this->smarty->assign('this', $this);

            $base_url = Kohana::config('sites/'.APPNAME.'.core.parent_base_url', False, False) ?
                        Kohana::config('sites/'.APPNAME.'.core.parent_base_url') :
                        url::base();
            $this->smarty->assign('arag_base_url', $base_url);
            $this->smarty->assign('arag_appname', APPNAME);
            $this->smarty->assign('arag_masterapp', MASTERAPP);
            $this->smarty->assign('arag_current_module', Router::$module);

            // Add current module plugins directory to plugins_dir
            $this->smarty->plugins_dir[] = MODPATH.Router::$module.'/views/plugins';
            array_unique($this->smarty->plugins_dir);

            // Fetch the output
            $output = $this->smarty->fetch($template);

        } else {
            $output = parent::_kohana_load_view($template, $vars);
        }


        return $output;
    }
    // }}}
    // {{{ __call
    public function __call($method, $arguments)
    {
        if (substr($method, 0, 1) == '_') {
            // The method is protected so just call 404
            Event::run('system.404');
        }

        // Set the method of Router
        Router::$method = $method;

        $alt_validator = $method . '_validate_' . Router::$request_method;
        $validator     = $method . '_validate_any';
        $validator     = method_exists($this, $alt_validator)
                       ? (method_exists($this, $alt_validator) ? $alt_validator : False)
                       : (method_exists($this, $validator) ? $validator : False);

        $validated = True;

        if ($validator != False) {
            $validated = $this->_call($validator, $arguments);

            if (!$validated && Kohana::config('smarty.integration') == True) {
                // An error occured so repopulate it to smarty templates
                $data = $this->validation->as_array();
                foreach ($data as $field => $value) {
                    $this->smarty->assign($field, $value);
                }
            }
        }

        $alt_method = $method . '_' . Router::$request_method;
        $alt_method = ($validated) ? $alt_method : $alt_method . '_error';
        $method     = ($validated) ? $method . '_any' : $method . '_any_error';

        // Is method exists?
        $method = method_exists($this, $alt_method)
                ? (method_exists($this, $alt_method) ? $alt_method : False)
                : (method_exists($this, $method) ? $method : False);

        if ($method == False) {
            // There is no method, try to find _default method
            if (method_exists($this, '_default')) {

                $this->_default(Router::$method, Router::$arguments);
                return;

            } else {
                Event::run('system.404');
            }
        }

        $this->_call($method, $arguments);
    }
    // }}}
    // {{{ _call
    private function _call($method, $arguments)
    {
        $result = True;

        if (empty($arguments)) {
            // Call the controller method with no arguments
            $result = $this->$method();

        } else {

            // Manually call the request method for up to 4 arguments. Why? Because
            // call_user_func_array is ~3 times slower than direct method calls.
            switch(count($arguments))
            {
                case 1:
                    $result = $this->$method($arguments[0]);
                break;
                case 2:
                    $result = $this->$method($arguments[0], $arguments[1]);
                break;
                case 3:
                    $result = $this->$method($arguments[0], $arguments[1], $arguments[2]);
                break;
                case 4:
                    $result = $this->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                break;
                default:
                    // Resort to using call_user_func_array for many segments
                    $result = call_user_func_array(array($this, $method), $arguments);
                break;
            }
        }

        return $result;
    }
    // }}}
    // {{{ _invalid_request
    public function _invalid_request($uri = Null, $message = Null)
    {
        $this->session->set('_invalid_request_uri', $uri);
        $this->session->set('_invalid_request_message', $message);
        $trace = debug_backtrace();
        Kohana::log('error', 'Invalid request: '.$trace[0]['file'].':'.$trace[0]['line']);
        url::redirect('invalid_request');
    }
    // }}}
    // {{{ _display
    public function _display()
    {
        if ($this->layout instanceof View) {
            isset($this->layout->content) AND $this->layout->content = $this->layout->content->render(); //We render content before main layout in order to make things 
            $this->layout->render(True);
        }
    }
    // }}}
}
