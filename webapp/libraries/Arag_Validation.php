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
        if (is_array($array)) {
            return $array = new Validation($array);

        } elseif (Router::$request_method === 'read' && is_array(Router::$arguments)) {
            return new Validation(Router::$arguments);
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
    // {{{ get_names
    public function get_names()
    {
        return $this->names;
    }
    // }}}
    // {{{ get_messages
    public function get_messages()
    {
        return $this->messages;
    }
    // }}}
    // {{{ get_errors
    public function get_errors()
    {
        return $this->errors;
    }
    // }}}
    // {{{ add_error
    public function add_error($field, $name)
    {
        $this->errors[$field] = $name;
        return $this;
    }
    // }}}

} // End Validation
