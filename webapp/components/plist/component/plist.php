<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

// {{{ List

/*
 * Class for create paginated list
 * 
 * @author  Armen Baghumian <armen@OpenSourceClub.org>
 * @since   PHP 5
 */

class PList extends Component implements IteratorAggregate, ArrayAccess
{
    // {{{ properties
    
    private $resource;
    private $columns        = Array();
    private $virtualColumns = Array();
    private $actions        = Array();
    private $groupActions   = Array();    
    private $baseURI        = Array();

    private $limit    = 0;   // How many results per page. 0 is infinity
    private $offset   = 0;   // Offset
    private $page     = 1;   // The page to start listing
    private $maxpages = 10;  // How many pages to show (google style) --> set false to disable

    const NONE           = Null;
    const HIDDEN_COLUMN  = 1;
    const VIRTUAL_COLUMN = 2;

    const GROUP_ACTION = 1;

    // PList properties
    const CAPTION  = 1;
    const HEADER   = 2;
    const FOOTER   = 4;
    const SORTABLE = 8; 

    private $properties = 0;

    // }}}
    // {{{ Constructor
    function __construct($namespace)
    {
        parent::__construct();

        $this->setResource(Array());
        $this->setProperties(self::CAPTION | self::HEADER | self::FOOTER | self::SORTABLE);

        // We'll need url helper
        $CI =& get_instance();
        $CI->load->helper('url');

        // Set default URI
        $uri = $CI->uri->uri_string();
        $uri = (substr($uri, -1) == '/') ? $uri : $uri . '/'; // Add trailing slash

        // If namespace is not empty add an underscore at the begining
        $namespace = ($namespace) ? '_'.$namespace : $namespace;

        if (preg_match('/page'.$namespace.':([0-9]+)*\//', $uri, $matchs)) {
            // Check if page parameter is already in uri
            $this->page = $matchs[1];
            $uri        = str_replace($matchs[0], '#page#/', $uri);
        } else {
            // Okey uri is clean so append #page# to uri
            $uri .= '#page#';
        }

        $this->setURI($uri);
    }
    // }}}
    // {{{ setResource
    function setResource($resource)
    {
        if (is_array($resource)) {
            $resource = new IteratorIterator(new ArrayIterator($resource));
        }
        
        if ($resource instanceof Traversable) {
            $this->resource = $resource;
            return;
        }

        throw new Exception('The specified resource is not valid resource.');
    }
    // }}}
    // {{{ setLimit
    function setLimit($limit, $offset = 0) 
    {
        if (!is_numeric($limit) || $limit < 0) {
            throw new Exception('Limit should be numeric greater than or equal to 0.');
        }

        $this->limit = $limit;

        if (is_numeric($offset) && $offset > 0) {
            $this->offset = $offset;
        }
    }
    // }}}
    // {{{ setURI
    function setURI($uri)
    {
        if (is_string($uri)) {
            $uri = explode('/', $uri);
        }

        if (is_array($uri)) {
            $this->baseURI = $uri;
        }
    }
    // }}}
    // {{{ getURI
    function getURI()
    {
        return $this->baseURI;
    }
    // }}}
    // {{{ setProperties
    function setProperties($properties)
    {
        $this->properties = ($this->properties != 0) ? $this->properties & $properties : $properties;
    }
    // }}}
    // {{{ getProperties
    function getProperties()
    {
        return $this->properties;
    }
    // }}}
    // {{{ hasHeader
    function hasHeader()
    {
        return $this->properties & self::HEADER;
    }
    // }}}
    // {{{ hasFooter
    function hasFooter()
    {
        return $this->properties & self::FOOTER;
    }
    // }}}
    // {{{ hasCaption
    function hasCaption()
    {
        return $this->properties & self::CAPTION;
    }
    // }}}
    // {{{ isSortable
    function isSortable()
    {
        return $this->properties & self::SORTABLE;
    }
    // }}}
    // {{{ addColumn
    function addColumn($name, $label = Null, $type = PList::NONE)
    {
        $label = ($label == Null) ? $name : $label;

        if ($type & PList::VIRTUAL_COLUMN) {
            $this->virtualColumns[$name] = Array('label' => $label);
        }

        $this->columns[$name] = Array ('label'   => $label, 
                                       'hidden'  => $type & PList::HIDDEN_COLUMN,
                                       'virtual' => $type & PList::VIRTUAL_COLUMN);
    }
    // }}}
    // {{{ addAction
    function addAction($uri, $label, $className = Null, $alternateCallback = False)
    {
        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        // Is it a group action?
        if ($alternateCallback === self::GROUP_ACTION) {

            $this->groupActions[] = Array('uri'             => $uri,
                                          'label'           => $label,
                                          'class_name'      => $className,
                                          'class_attribute' => ($className != Null) ? 'class="'.$className.'"' : Null);
        } else {

            $this->actions[] = Array('uri'                => $uri,
                                     'label'              => $label,
                                     'class_name'         => $className,
                                     'class_attribute'    => ($className != Null) ? 'class="'.$className.'"' : Null,
                                     'alternate_callback' => $alternateCallback);
        }
    }
    // }}}
    // {{{ addGroupAction
    function addGroupAction($uri, $label, $className = Null)
    {
        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        $this->groupActions[] = Array('uri'                => $uri,
                                      'label'              => $label,
                                      'class_name'         => $className,
                                      'class_attribute'    => ($className != Null) ? 'class="'.$className.'"' : Null,
                                      );
    }
    // }}}    
    // {{{ & getVirtualColumns
    function & getVirtualColumns()
    {
        return $this->virtualColumns;
    }
    // }}}
    // {{{ & getActions
    function & getActions()
    {
        return $this->actions;
    }
    // }}}
    // {{{ & getGroupActions
    function & getGroupActions()
    {
        return $this->groupActions;
    }
    // }}}    
    // {{{ getActionsCount
    function getActionsCount()
    {
        return count($this->actions);
    }
    // }}}
    // {{{ & getColumns
    function & getColumns()
    {
        return $this->columns;
    }
    // }}}
    // {{{ & getColumnNames
    function & getColumnNames()
    {
        $columnNames = array_keys($this->columns);
        return $columnNames;
    }
    // }}}
    // {{{ parseURI
    function parseURI($uri, $row = Array())
    {
        $pattern = '/#(.+?)#/';

        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        if (preg_match($pattern, $uri, $matchs)) {

            // Checking for url Variables
            if (is_array($row) && array_key_exists($matchs[1], $row)) {
                $uri = str_replace("#{$matchs[1]}#", $row[$matchs[1]], $uri);
            
            } else if (is_string($row) && preg_match('/([a-zA-z_][a-zA-Z_0-9]*)=([a-zA-Z_0-9:]+);/', $row, $matchs)) {
                $uri = str_replace("#{$matchs[1]}#", $matchs[2], $uri);
            }
        }

        return $uri;
    }
    // }}}
    // {{{ callCallback
    function callCallback($callback, $row = Array())
    {
        // There is an array argument to pass to the callback
        $arg = Array($row);

        if (strpos($callback, '.') !== false) {
            // Model and function separated with a dot
            list($modelName, $functionName) = explode('.', $callback);

            $CI =& get_instance();
            $CI->load->model($modelName);
            
            if (method_exists(&$CI->$modelName, $functionName)) {
                return call_user_func_array(array(&$CI->$modelName, $functionName), $arg);
            }
        
        } else if (strpos($callback, '::') !== false) {

            // Classname and function separated with a ::
            list($className, $functionName) = explode('::', $callback);

            if (method_exists($className, $functionName)) {
                return call_user_func_array(array($className, $functionName), $arg);
            }

        } else {
            // The function is in resource
            if (method_exists($this->resource, $callback)) {
                return call_user_func_array(array($this->resource, $callback), $arg);
            }            
        }

        throw new Exception('No such callback found: ' . $callback);
    }
    // }}}
    // {{{ getPager
    function getPager()
    {
        include_once 'pager.php';

        // Get pager result
        $pager = Pager::getData($this->page, $this->limit, count(iterator_to_array($this->resource)), $this->maxpages);

        // Set offset to fetch what we need to get depend on what page we are in
        $this->setLimit($this->limit, (($pager['from'] - 1) < 0)?0:$pager['from'] - 1);

        return $pager;
    }
    // }}}
    // {{{ getIterator
    function getIterator()
    {
        $limit = ($this->limit <= 0) ? -1 : $this->limit;
        return new LimitIterator($this->resource, $this->offset, $limit);
    }
    // }}}
    // {{{ offsetExists
    function offsetExists($offset)
    {
        $resource = iterator_to_array($this->resource);
        if (isset($resource[$offset])) { 
            return True;
        }

        return False;
    }   
    // }}}
    // {{{ offsetGet
    function offsetGet($offset)
    {
        $resource = iterator_to_array($this->resource);
    
        if ($this->offsetExists($offset)) {
            return $resource[$offset];
        }

        return False;
    }
    // }}}
    // {{{ offsetSet
    function offsetSet($offset, $value)
    {
        // Readonly
    }
    // }}}
    // {{{ offsetUnset
    function offsetUnset($offset)
    {
        // Readonly
    }
    // }}}
}
// }}}

?>
