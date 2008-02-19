<?php
/**
 * Validation library.
 *
 * $Id$
 *
 * @package    Validation
 */
class Validation extends Validation_Core {

    // {{{ Properties

    // Names
    protected $names = array();

    // }}}
    // {{{ factory
    public static function factory($array = NULL)
    {
        $data = (Router::$request_method === 'read' && is_array(Router::$arguments[1])) 
              ? Router::$arguments[1] 
              : $_POST;

        return new Validation( ! is_array($array) ? $data : $array);
    }
    // }}}
    // {{{ name
    /**
     * @chainable
     * @param     string   Field
     * @param     string   Field name
     */
    public function name($field, $name)
    {
        $this->names[$field] = $name;

        return $this;
    }
    // }}}
    // {{{ names
    public function & names()
    {
        return $this->names;
    }
    // }}}

} // End Validation
