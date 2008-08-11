<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class ErrorStack
{
    // {{{ properties

    private $stack                = Array();
    private $errorMessageTemplate = Array();

    // }}}
    // {{{ instance
    public function instance()
    {
        static $instance;

		// Create the singleton
        empty($instance) AND $instance = new ErrorStack;

        return $instance;
    }
    // }}}
    // {{{ push
    public function push($code, $level = 'error', $params = array(), $message = false)
    {
        if (is_array($params) && !empty($params) && isset($this->errorMessageTemplate[$code])) {
            $message = $this->errorMessageTemplate[$code];

            foreach ($params as $name => $value) {
                if (strpos($message, $name) !== False) {
                    $message = str_replace('%'.$name.'%', $value, $message);
                }
            }
        }

        // Save error
        $time  = explode(' ', microtime());
        $error = Array
        (
            'code'    => $code,
            'params'  => $params,
            'level'   => $level,
            'time'    => $time[1] + $time[0],
            'message' => $message
        );

        $this->stack[$level][] = $error;
    }
    // }}}
    // {{{ getErrors
    public function getErrors($level = 'error')
    {
        return $this->stack[$level];
    }
    // }}}
    // {{{ hasErrors
    public function hasErrors()
    {
        return (bool) count($this->stack);
    }
    // }}}
    // {{{ setErrorMessageTemplate
    public function setErrorMessageTemplate($errorMessageTemplate)
    {
        $this->errorMessageTemplate = $errorMessageTemplate;
    }
    // }}}
}
