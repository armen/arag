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

class TabbedBlock_Component extends Component
{
    // {{{ properties

    var $_items      = Array();
    var $_title      = Null;
    var $_parameters = Array();
    var $_attributes = Array();
    var $_template   = 'arag_tabbed_block';

    // }}}
    // {{{ Constructor
    public function __construct($namespace = Null)
    {
        parent::__construct($namespace);
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
    // {{{ setAttribute
    public function setAttribute($name, $value)
    {
        $attribute         = Array();
        $attribute[$name]  = $value;
        $this->_attributes = array_merge($this->_attributes, $attribute);

        return True;
    }
    // }}}
    // {{{ setAttributes
    public function setAttributes($attributes)
    {
        $this->_attributes = array_merge($this->_attributes, $attributes);

        return True;
    }
    // }}}
    // {{{ setTemplate
    public function setTemplate($template)
    {
        $this->_template = $template;
    }
    // }}}
    // {{{ getTemplate
    public function getTemplate()
    {
        return $this->_template;
    }
    // }}}
    // {{{ getTitle
    public function getTitle()
    {
        return $this->replaceParameter($this->_title);
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
    // {{{ replaceParameter
    public function replaceParameter($parameter)
    {
        if (preg_match('/%(.+?)%/', $parameter, $matches)) {

            $value     = isset($this->_parameters[$matches[1]]) ? $this->_parameters[$matches[1]] : Null;
            $parameter = str_replace("%{$matches[1]}%", $value, $parameter);
        }

        return $parameter;
    }
    // }}}

}

?>
