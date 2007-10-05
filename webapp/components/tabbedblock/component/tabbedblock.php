<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

// {{{ class TabbedBlock

/*
 * Class for create Tabbed page
 * 
 * @author  Armen Baghumian <armen@OpenSourceClub.org>
 * @since   PHP 5
 */

class TabbedBlock extends Component
{
    // {{{ properties
    
    var $_items      = Array();
    var $_title      = Null;
    var $_parameters = Array();
        
    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();
    }
    // }}}
    // {{{ setTitle
    function setTitle($title)
    {
        $this->_title = $title;
    }
    // }}}
    // {{{ addItem
    function addItem($name, $uri, $title = Null, $enabled = True)
    {
        $items = Array( 'name'    => $name, 
                        'uri'     => $uri,
                        'enabled' => $enabled,
                        'title'   => ($title == Null) ? $name : $title);

        $this->_items[] = $items;
    }
    // }}}
    // {{{ & getItems
    function & getItems()
    {
        return $this->_items;
    }
    // }}}
    // {{{ setParameter
    function setParameter($name, $value)
    {
        $params = Array();
        $params[$name] = $value;
        $this->_parameters = array_merge($this->_parameters, $params);

        return True;
    }
    // }}}
    // {{{ setParameters
    function setParameters($params)
    {
        $this->_parameters = array_merge($this->_parameters, $params);

        return True;
    }
    // }}}
    // {{{ getTitle
    function getTitle()
    {
        return $this->_title;
    }
    // }}}
    // {{{ parseURI
    function parseURI($uri)
    {
        $pattern = '/%(.+?)%/';

        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        if (preg_match($pattern, $uri, $matchs)) {

            // Checking for url Variables
            if (array_key_exists($matchs[1], $this->_parameters)) {
                $uri = str_replace("%{$matchs[1]}%", $this->_parameters[$matchs[1]], $uri);
            }
        }

        return $uri;
    }
    // }}}
    // {{{ genURL
    function genURL($uri, $parseURI = True)
    {
        $CI =& get_instance();

        if ($parseURI) {
            $uri = $this->parseURI($uri);
        }

        if (strpos($uri, 'http://') === 0 || strpos($uri, 'https://') === 0) {
            // http:// or https:// is at begining of uri
            return $uri;
        } else {
            return $CI->config->item('base_url') . $CI->config->item('index_page') . '/' . $uri;
        }
    }
    // }}}
}
// }}}

?>
