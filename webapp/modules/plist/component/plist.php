<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

/*
 * Class for create paginated list
 * 
 * @author  Armen Baghumian <armen@OpenSourceClub.org>
 * @since   PHP 5
 */

class PList_Component extends Component implements IteratorAggregate, ArrayAccess
{
    // {{{ properties
    
    private $resource;
    private $columns        = Array();
    private $virtualColumns = Array();
    private $actions        = Array();
    private $baseURI        = Array();

    private $groupActions             = Array();
    private $groupActionType          = 'button';
    private $groupActionParameterName = 'id';

    private $limit    = 0;   // How many results per page. 0 is infinity
    private $offset   = 0;   // Offset
    private $page     = 1;   // The page to start listing
    private $maxpages = 10;  // How many pages to show (google style) --> set false to disable

    private $emptyListMessage = "List content is Empty!";

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
    function __construct($namespace = Null)
    {
        parent::__construct($namespace);

        $this->setResource(Array());
        $this->setProperties(self::CAPTION | self::HEADER | self::FOOTER | self::SORTABLE);

        $Controller = Kohana::instance();

        // Set default URI
        $directory = substr(Router::$directory, strpos(Router::$directory, 'controllers/') + 12); // 12 is strlen('controllers/')
        $uri       = Router::$module . '/' . $directory . Router::$controller . '/' . Router::$method . '/' . implode('/', Router::$arguments);
        $uri       = rtrim($uri, '/') . '/'; // Add trailing slash

        // If namespace is not empty add an underscore at the begining
        $namespace = ($namespace) ? '_'.$namespace : $namespace;

        if (preg_match('/page'.$namespace.':([0-9]+)*\//', $uri, $matches)) {
            // Check if page parameter is already in uri
            $this->page = $matches[1];
            $uri        = str_replace($matches[0], '#page#/', $uri);
        } else {
            // Okey uri is clean so append #page# to uri
            $uri .= '#page#';
        }

        $this->setURI($uri);
    }
    // }}}
    // {{{ setResource
    public function setResource($resource)
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
    public function setLimit($limit, $offset = 0) 
    {
        if (!is_numeric($limit) || $limit < 0) {
            $limit = 0;
        }

        $this->limit = $limit;

        if (is_numeric($offset) && $offset > 0) {
            $this->offset = $offset;
        }
    }
    // }}}
    // {{{ setURI
    public function setURI($uri)
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
    public function getURI()
    {
        return $this->baseURI;
    }
    // }}}
    // {{{ setProperties
    public function setProperties($properties)
    {
        $this->properties = ($this->properties != 0) ? $this->properties & $properties : $properties;
    }
    // }}}
    // {{{ getProperties
    public function getProperties()
    {
        return $this->properties;
    }
    // }}}
    // {{{ setGroupActionType
    public function setGroupActionType($type)
    {
        $this->groupActionType = $type;
    }
    // }}}
    // {{{ getGroupActionType
    public function getGroupActionType()
    {
        return $this->groupActionType;
    }
    // }}}    
    // {{{ hasHeader
    public function hasHeader()
    {
        return $this->properties & self::HEADER;
    }
    // }}}
    // {{{ hasFooter
    public function hasFooter()
    {
        return $this->properties & self::FOOTER;
    }
    // }}}
    // {{{ hasCaption
    public function hasCaption()
    {
        return $this->properties & self::CAPTION;
    }
    // }}}
    // {{{ isSortable
    public function isSortable()
    {
        return $this->properties & self::SORTABLE;
    }
    // }}}
    // {{{ addColumn
    public function addColumn($name, $label = Null, $type = PList_Component::NONE)
    {
        $label = ($label == Null) ? $name : $label;

        if ($type & PList_Component::VIRTUAL_COLUMN) {
            $this->virtualColumns[$name] = Array('label' => $label);
        }

        $this->columns[$name] = Array ('label'   => $label, 
                                       'hidden'  => $type & PList_Component::HIDDEN_COLUMN,
                                       'virtual' => $type & PList_Component::VIRTUAL_COLUMN);
    }
    // }}}
    // {{{ addAction
    public function addAction($uri, $label, $className = Null, $alternateCallback = False, $alternateUri = Null, $target = FALSE)
    {
        $title = Null;

        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        if (is_array($label)) {
            @list($label, $title) = $label;
        }

        // Is it a group action?
        if ($alternateCallback === self::GROUP_ACTION) {

            $this->groupActions[] = Array('uri'             => $uri,
                                          'label'           => $label,
                                          'class_name'      => $className,
                                          'class_attribute' => ($className != Null) ? 'class="'.$className.'"' : Null,
                                          'title'           => ($title == Null) ? $label : $title);
        } else {

            $this->actions[] = Array('uri'                => $uri,
                                     'label'              => $label,
                                     'target'             => ($target) ? '_blank' : '_self',
                                     'class_name'         => $className,
                                     'class_attribute'    => ($className != Null) ? 'class="'.$className.'"' : Null,
                                     'alternate_callback' => $alternateCallback,
                                     'alternate_uri'      => $alternateUri,
                                     'title'              => ($title == Null) ? $label : $title);
        }
    }
    // }}}
    // {{{ & getVirtualColumns
    public function & getVirtualColumns()
    {
        return $this->virtualColumns;
    }
    // }}}
    // {{{ & getActions
    public function & getActions()
    {
        return $this->actions;
    }
    // }}}
    // {{{ & getGroupActions
    public function & getGroupActions()
    {
        return $this->groupActions;
    }
    // }}}    
    // {{{ getActionsCount
    public function getActionsCount()
    {
        return count($this->actions);
    }
    // }}}
    // {{{ setGroupActionParameterName
    public function setGroupActionParameterName($name)
    {
        $this->groupActionParameterName = $name;
    }
    // }}}
    // {{{ getGroupActionParameterName
    public function getGroupActionParameterName()
    {
        return $this->groupActionParameterName;
    }
    // }}}    
    // {{{ getEmptyListMessage
    public function getEmptyListMessage()
    {
        return $this->emptyListMessage;
    }
    // }}}
    // {{{ setEmptyListMessage
    public function setEmptyListMessage($message)
    {
        $this->emptyListMessage = $message;
    }
    // }}}
    // {{{ & getColumns
    public function & getColumns()
    {
        return $this->columns;
    }
    // }}}
    // {{{ & getColumnNames
    public function & getColumnNames()
    {
        $columnNames = array_keys($this->columns);
        return $columnNames;
    }
    // }}}
    // {{{ parseURI
    public function parseURI($uri, $row = Array())
    {
        $pattern = '/#(.+?)#/';

        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        if (preg_match($pattern, $uri, $matches)) {

            // Checking for url Variables
            if (is_array($row) && array_key_exists($matches[1], $row)) {
                $uri = str_replace("#{$matches[1]}#", $row[$matches[1]], $uri);
            
            } else if (is_string($row) && preg_match('/([a-zA-z_][a-zA-Z_0-9]*)=([a-zA-Z_0-9:]+);/', $row, $matches)) {
                $uri = str_replace("#{$matches[1]}#", $matches[2], $uri);
            }
        }

        return $uri;
    }
    // }}}
    // {{{ callCallback
    public function callCallback($callback, $row = Array())
    {
        // There is an array argument to pass to the callback
        $arg = Array($row);

        if (strpos($callback, '.') !== false) {
            // Model and function separated with a dot
            list($modelName, $functionName) = explode('.', $callback);

            $modelName = new $modelName.'_Model';
            
            if (method_exists($modelName, $functionName)) {
                return call_user_func_array(array($modelName, $functionName), $arg);
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
    public function getPager()
    {
        include_once 'pager.php';

        // Get pager result
        $pager = Pager::getData($this->page, $this->limit, count(iterator_to_array($this->resource)), $this->maxpages);

        // Set offset to fetch what we need to get depend on what page we are in
        $this->setLimit($this->limit, (($pager['from'] - 1) < 0)?0:$pager['from'] - 1);

        return $pager;
    }
    // }}}
    // {{{ getResourceCount
    public function getResourceCount()
    {
        return count(iterator_to_array($this->resource));
    }
    // }}}
    // {{{ getIterator
    public function getIterator()
    {
        $limit = ($this->limit <= 0) ? -1 : $this->limit;
        return new LimitIterator($this->resource, $this->offset, $limit);
    }
    // }}}
    // {{{ offsetExists
    public function offsetExists($offset)
    {
        $resource = iterator_to_array($this->resource);
        if (isset($resource[$offset])) { 
            return True;
        }

        return False;
    }   
    // }}}
    // {{{ offsetGet
    public function offsetGet($offset)
    {
        $resource = iterator_to_array($this->resource);
    
        if ($this->offsetExists($offset)) {
            return $resource[$offset];
        }

        return False;
    }
    // }}}
    // {{{ offsetSet
    public function offsetSet($offset, $value)
    {
        // Readonly
    }
    // }}}
    // {{{ offsetUnset
    public function offsetUnset($offset)
    {
        // Readonly
    }
    // }}}
}

?>
