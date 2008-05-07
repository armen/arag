<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id: category.php 432 2007-12-08 07:56:37Z sasan $
// ---------------------------------------------------------------------------

/*
 * Class for create categories
 * 
 * @author  Sasan Rose <sasan.rose@gmail.com>
 * @since   PHP 5
 */

class Category_Component extends Component
{
    // {{{ Properties
    
    private $module;
    private $categories       = Null;
    private $category;
    private $level            = 0;
    private $baseURI          = array();
    private $parentURI;
    private $emptyListMessage = "List content is Empty!";
    private $columns          = 3;
    private $finalURI         = false;

    // }}}
    // {{{ construct
    public function __construct($namespace = Null)
    {
        parent::__construct($namespace);
        $this->level    = 0;
        $this->category = new Category_Model;
        $this->setModule();

        $Controller = Kohana::instance();

        // Set default URI
        $directory = substr(Router::$directory, strpos(Router::$directory, 'controllers/') + 12); // 12 is strlen('controllers/')
        $uri       = Router::$module . '/' . $directory . Router::$controller . '/' . Router::$method . '/' . implode('/', Router::$arguments);
        $uri       = rtrim($uri, '/') . '/'; // Add trailing slash

        // If namespace is not empty add an underscore at the begining
        $namespace = ($namespace) ? '_'.$namespace : $namespace;

        if (preg_match('/parent'.$namespace.':([0-9]+)*\//', $uri, $matches)) {
            // Check if page parameter is already in uri
            $this->level = $matches[1];
            $uri         = str_replace($matches[0], '', $uri);
        }

        $this->parentURI  = 'parent'.$namespace.':';

        $this->setURI($uri);
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
    // {{{ parseURI
    public function parseURI($uri, $id = NULL, $row = NULL)
    {
        $pattern = '/#(.+?)#/';

        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        if (preg_match($pattern, $uri, $matches)) {

            // Checking for url Variables
            //if (is_array($row) && array_key_exists($matches[1], $row)) {
                $uri = str_replace("$matches[0]", $row, $uri);

            //} else if (is_string($row) && preg_match('/([a-zA-z_][a-zA-Z_0-9]*)=([a-zA-Z_0-9:]+);/', $row, $matches)) {
              //  $uri = str_replace("#{$matches[1]}#", $matches[2], $uri);
           // }
        }

        if ($id != NULL) {
            $uri = $uri.$this->parentURI.$id;

        }
        
        return $uri;
    }
    // }}}
    // {{{ setModule
    public function setModule($module = Null)
    {
        $this->module = empty($module) ? Router::$module : $module;
    }
    // }}}
    // {{{ getModule
    public function getModule()
    {
        return $this->module;
    }
    // }}}
    // {{{ build
    public function build()
    {
        $this->categories = $this->category->getCategories($this->module, $this->level, 'name');
        return $this->categories;
    }
    // }}}
    // {{{ recursiveBuild
    public function recursiveBuild()
    {

    }
    // }}}
    // {{{ getDirctoriesCount
    public function getSubCatCount($module = NULL, $parent_id = NULL)
    {
        if ($module == NULL) {
            $module = $this->module;
        }

        if ($parent_id == NULL) {
            $parent_id = $this->level;
        }

        return $this->category->getCatNumbers($module, (integer) $parent_id);
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
    // {{{ setColumns
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }
    // }}}
    // {{{ getColumns
    public function getColumns()
    {
        return $this->columns;
    }
    // }}}
    // {{{ setFinalURI
    public function setFinalURI($uri)
    {
        $this->finalURI = $uri;
    }
    // }}}
    // {{{ getFinalURI
    public function getFinalURI()
    {
        return $this->finalURI;
    }
    // }}}
    // {{{ getBreadCrumb
    public function getBreadCrumb()
    {
        $category     = $this->category->getCategory($this->level);
        $has_category = $this->category->hasCategory($this->level);
        $bread_crumb  = array();

            while ($has_category) {
                $bread_crumb[$category['id']] = $category['name'];

                $has_category  = $this->category->hasCategory($category['parent_id']);
                $category      = $this->category->getCategory($category['parent_id']);

            }

        $bread_crumb[0] = 'top';

        return (array_reverse($bread_crumb, true));
    }
    // }}}
}
?>
