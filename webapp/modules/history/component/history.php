<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

/*
 * Class for create Tabbed page
 *
 * @author  Armen Baghumian <armen@OpenSourceClub.org>
 * @since   PHP 5
 */

class History_Component extends Component
{
    // {{{ Constructor
    public function __construct($namespace = Null)
    {
        parent::__construct($namespace);

        $session = Session::instance();
        $history = $session->get('history.'.Router::$module, Array('rsegments' => Array(), 'uri_map' => Array()));

        $rsegments = implode('/', Router::$rsegments);
        $title     = (Kohana::instance()->layout->title) ? Kohana::instance()->layout->title : ucwords(str_replace('_', ' ', Router::$method));

        $history['rsegments'][] = $rsegments;
        $history['uri_map'][]   = Router::$current_uri;
        $history['titles'][]    = _($title);

        // Search for matching uri in history
        $offset = array_search($rsegments, $history['rsegments']);

        array_splice($history['rsegments'], $offset, count($history['rsegments']), $rsegments);
        array_splice($history['uri_map'], $offset, count($history['uri_map']), Router::$current_uri);
        array_splice($history['titles'], $offset, count($history['titles']), _($title));

        $session->set_flash('history.'.Router::$module, $history);
    }
    // }}}
    // {{{ get_history
    public function get_history()
    {
        $session = Session::instance();
        return $session->get('history.'.Router::$module, Array('rsegments' => Array(), 'uri_map' => Array()));
    }
    // }}}
}

?>
