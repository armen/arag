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
    public static function factory(&$array = NULL)
    {
        $data = array_slice(Router::$arguments, 1);

        if (is_array($array)) {
            return $array = new Validation($array);

        } elseif (Router::$request_method === 'read' && is_array($data)) {
            return new Validation($data);
        }

        return $_POST = new Validation(array_merge($_POST, arr::standardize_files_array($_FILES)));
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
        return ((int) $field >= (int) current($inputs));
    }
    // }}}
    // {{{ max
    public function max($field, array $inputs)
    {
        return ((int) $field <= (int) current($inputs));
    }
    // }}}

} // End Validation
