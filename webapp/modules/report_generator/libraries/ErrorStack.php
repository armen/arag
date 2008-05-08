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

    private $stack = Array();

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
    public function push($code, $level = 'error', $params = array(), $message = false);
    {
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
        return (bool) count($this->stack[$level]);
    }
    // }}}
    // {{{ setErrorMessageTemplate
    public function setErrorMessageTemplate($errorMessageTemplate)
    {
        $this->errorMessageTemplate = $errorMessageTemplate;
    }
    // }}}
}
