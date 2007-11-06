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
    public function setTitle($title)
    {
        $this->_title = $title;
    }
    // }}}
    // {{{ addItem
    public function addItem($name, $uri, $parentUri = Null, $title = Null, $enabled = True, $selected = False)
    {
        $item = Array('name'                => $name, 
                      'uri'                 => $uri,
                      'is_url'              => (boolean) preg_match('!^\w+://!i', $uri),
                      'selected'            => $selected,
                      'is_parent'           => True,
                      'has_selected_subtab' => False,
                      'enabled'             => $enabled,
                      'title'               => ($title == Null) ? $name : $title);

        if ($parentUri == Null) {
            $this->_items[md5($uri.$parentUri)] = $item;

        } else if ($parentUri != Null && isset($this->_items[md5($parentUri)])) {

            $this->_items[md5($uri.$parentUri)] = $item;            
            $this->_items[md5($uri.$parentUri)]['parent_uri'] = $parentUri;
            $this->_items[md5($uri.$parentUri)]['is_parent']  = False;
        }
    }
    // }}}
    // {{{ & getItems
    public function & getItems()
    {
        return $this->_items;
    }
    // }}}
    // {{{ setParameter
    public function setParameter($name, $value)
    {
        $params = Array();
        $params[$name] = $value;
        $this->_parameters = array_merge($this->_parameters, $params);

        return True;
    }
    // }}}
    // {{{ setParameters
    public function setParameters($params)
    {
        $this->_parameters = array_merge($this->_parameters, $params);

        return True;
    }
    // }}}
    // {{{ getTitle
    public function getTitle()
    {
        return $this->_title;
    }
    // }}}
    // {{{ parseURI
    public function parseURI($uri)
    {
        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        if (preg_match('/%(.+?)%/', $uri, $matches)) {

            $parameter = isset($this->_parameters[$matches[1]]) ? $this->_parameters[$matches[1]] : Null;
            $uri       = str_replace("%{$matches[1]}%", $parameter, $uri);
    
            // Check for another variable
            return $this->parseURI($uri);
        }

        return $uri;
    }
    // }}}
    // {{{ genURL
    public function genURL($uri, $parseURI = True)
    {
        if ($parseURI) {
            $uri = $this->parseURI($uri);
        }

        if (preg_match('!^\w+://!i', $uri)) {
            // there is \w:// at begining of uri            
            return $uri;
        } else {
            return url::site($uri);
        }
    }
    // }}}
}

?>
