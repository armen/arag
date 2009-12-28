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
            $theme         = Kohana::config('theme.default');
            $content_types = Kohana::config('core.content_types');

            $content_type = Router::$content_type ? Router::$content_type : 'default';

            if (isset($content_types[$content_type])) {

                if (isset($content_types[$content_type]['layout'])) {
                    $this->layout = $content_types[$content_type]['layout'];
                } else {
                    $prefix = (strpos(Router::$controller_path, 'backend') !== False || Router::$controller === 'backend')
                            ? 'backend_'
                            : 'frontend_';

                    $this->layout = $content_types[$content_type][$prefix.'layout'];
                }
            }

            $this->layout = str_replace('%theme_name%', $theme, $this->layout);
        }

        if ($this->layout == Null) {
            throw new Exception('There is no layout for current content_type, please set a layout in core.content_types');
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

        $messaging                    = Model::load('Messaging', 'messaging');
        $this->layout->messages_count = $messaging->getReadMessagesCount($this->session->get('user.username'));
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

            // Save current template_vars
            $template_vars = $this->smarty->get_template_vars();

            // Assign variables to the template
            if (is_array($vars) && count($vars) > 0) {
                foreach ($vars as $key => $val) {
                    $this->smarty->assign($key, $val);
                }
            }

            // Send Kohana::instance and base url to all templates
            $this->smarty->assign('this', $this);

            if (PHP_SAPI != 'cli') {
                $base_url = Kohana::config('sites/'.APPNAME.'.core.parent_base_url', False, False) ?
                            Kohana::config('sites/'.APPNAME.'.core.parent_base_url') :
                            url::base();
                $this->smarty->assign('arag_base_url', $base_url);
            }

            $this->smarty->assign('arag_appname', APPNAME);
            $this->smarty->assign('arag_masterapp', MASTERAPP);
            $this->smarty->assign('arag_current_module', Router::$module);
            $this->smarty->assign('arag_username', Session::instance()->get('user.username'));
            $this->smarty->assign('arag_current_theme', Kohana::config('theme.default'));
            $this->smarty->assign('arag_docroot', DOCROOT);
            $this->smarty->assign('arag_lang', Kohana::Config('locale.lang'));

            // Add current module plugins directory to plugins_dir
            $this->smarty->plugins_dir[] = MODPATH.Router::$module.'/views/plugins';
            array_unique($this->smarty->plugins_dir);

            // Fetch the output
            $output = $this->smarty->fetch($template);

            // Get rid of all variables of current template
            $this->smarty->_tpl_vars = $template_vars;

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
            return;
        }

        // Set the method of Router
        Router::$method = $method;

        $suffix = (Router::$content_type)
                ? '_' . Router::$content_type . '_' . Router::$request_method
                : '_' . Router::$request_method;

        $any_suffix = (Router::$content_type)
                    ? '_' . Router::$content_type . '_any'
                    : '_any';

        $alt_validator = $method . '_validate' . $suffix;
        $validator     = $method . '_validate' . $any_suffix;
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

        $alt_method = $method . $suffix;
        $alt_method = ($validated) ? $alt_method : $alt_method . '_error';
        $err_method = ($validated) ? $method . $any_suffix : $method . $any_suffix .'_error';

        // Is method exists?
        $method = method_exists($this, $alt_method)
                ? (method_exists($this, $alt_method) ? $alt_method : False)
                : (method_exists($this, $err_method) ? $err_method : False);

        if ($method == False) {
            // There is no method, try to find _default method
            if (method_exists($this, '_default')) {

                $this->_default(Router::$method, Router::$arguments);
                return;

            } else {
                if (strpos($alt_method, 'error') !== False) {
                    throw new Exception("Couldn't find any of the expected error handler methods, neither '{$alt_method}' nor '{$err_method}'!");
                }
                Event::run('system.404');
                return;
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
        url::redirect(ltrim(Router::$content_type.'/invalid_request', '/'));
    }
    // }}}
    // {{{ _display
    public function _display()
    {
        if ($this->layout instanceof View) {

            $theme = Kohana::config('theme.default');

            if (strpos(Router::$controller_path, 'backend') !== False || Router::$controller === 'backend') {
                $this->layout->content_wrapper = new View('themes/'.$theme.'/backend_content_wrapper');
            } else {
                $this->layout->content_wrapper = new View('themes/'.$theme.'/frontend_content_wrapper');
            }

            // We render content before main layout in order to make things
            if ($this->layout->content InstanceOf View) {
                $this->layout->content_wrapper->content = $this->layout->content->render();
                $this->layout->content_wrapper          = $this->layout->content_wrapper->render();
            }

            $this->layout->render(True);
        }
    }
    // }}}
    // {{{ execute
    public static function execute($uri, $return_content = True, $show_headers = False, $post = array(), $ajax = True)
    {
        // We want to simulate POST situation
        $post && ($_POST += $post);

        $GLOBALS['controller_execute'] = True;
        $GLOBALS['ajax']               = $ajax;
        $result                        = False;
        $theme                         = Kohana::config('theme.default');
        $old_current_uri               = Router::$current_uri;
        Router::$current_uri           = Router::routed_uri($uri);
        Router::request_method(empty($post) ? 'read' : 'write');
        Router::setup();

        if (strpos(Router::$controller_path, Router::$module) !== False) {
            $controller_path = Router::$controller_path;
        } else {
            $controller_path = MODPATH.Router::$module.'/'.substr(Router::$controller_path, strpos(Router::$controller_path, 'controllers/'));
        }

        if (file_exists($controller_path)) {

            // Save old include paths
            $old_include_paths = Kohana::include_paths();
            Kohana::config_set('core.modules', Array(MODPATH.Router::$module));

            include_once($controller_path);

            $controller = ucfirst(Router::$controller).'_Controller';
            $controller = new $controller;

            Event::clear('system.post_controller', array($controller, '_display'));

            $controller->layout               = new View('themes/'.$theme.'/empty_layout');
            $controller->layout->show_headers = $show_headers;  // This is usefull for ajax requests that are loaded independent of main content
            $controller->_call(Router::$method, Router::$arguments);

            if ($return_content) {
                if ($controller->layout->content instanceOf View) {
                    $controller->layout->content = $controller->layout->content->render();
                }
                $result = $controller->layout->render();
            } else {
                $result = $controller;
            }

            Kohana::config_set('core.modules', $old_include_paths);

        } else {
            Kohana::log('debug', 'Given controller path `'.$controller_path.'` does not exists in Arag_Controller::execute');
        }

        unset($GLOBALS['controller_execute']);
        unset($GLOBALS['ajax']);
        Router::$current_uri = $old_current_uri;
        Router::setup();

        return $result;
    }
    // }}}
}
