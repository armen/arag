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
        $data = array_slice(Router::$arguments, 1);
        $data = (Router::$request_method === 'read' && is_array($data)) ? $data : $_POST;

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
    // {{{ min
    public function min($field, array $inputs)
    {
        $result = False;
        if ((int)$field >= (int)current($inputs)) {
            $result = True;
        }
        return $result;
    }
    // }}}
    // {{{ max
    public function max($field, array $inputs)
    {
        $result = False;
        if ((int)$field <= (int)current($inputs)) {
            $result = True;
        }
        return $result;
    }
    // }}}

} // End Validation
