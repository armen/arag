<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

/*
 * Class for create breadcrumbs
 *
 * @author  Sasan Rose <sasan.rose@gmail.com>
 * @since   PHP 5
 */

class breadcrumb_Component extends Component
{
    // {{{ Properties
    public $real_uri;
    // }}}
    // {{{ Constructor
    public function __construct($namespace = Null)
    {
        parent::__construct($namespace);

        $destination    = implode('/', Router::$rsegments);
        $arguments      = implode('/', Router::$arguments);
        $this->real_uri = rtrim(str_replace($arguments, '', $destination), '/');
    }
    // }}}
    // {{{ get_visited_uris
    public function get_visited_uris($namespace)
    {
        $session = Session::instance();
        return $session->get('breadcrumb.'.$namespace, array());
    }
    // }}}
    // {{{ set_visited_uri
    public function set_visited_uri($namespace)
    {
        $session        = Session::instance();
        $visited_uris   = $this->get_visited_uris($namespace);

        if (!in_array($this->real_uri, $visited_uris)) {
            $visited_uris[] = $this->real_uri;
        }

        $session->set('breadcrumb.'.$namespace, $visited_uris);
    }
    // }}}
    // {{{ current_uri
    public function current_uri()
    {
        return $this->real_uri;
    }
    // }}}
    // {{{ get_config
    public function get_config($config_file, $module = Null)
    {
        return Arag_Config::get($config_file, Null, $module, True);
    }
    // }}}

}

?>
