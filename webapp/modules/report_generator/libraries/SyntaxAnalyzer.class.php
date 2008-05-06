<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class SyntaxAnalyzer
{
    // {{{ properties
    
    protected $lexer        = Null;
    protected $lookAhead    = Null;
    protected $symbolTable  = Null;
    protected $stack        = Null;   // Error stack

    // }}}
    // {{{ Constructor
    function __cosntruct($lexer, $stack)
    {
        // Set lexical analyzer object
        $this->lexer = $lexer;

        // Set error stack
        $this->stack = $stack;
    }
    // }}}
    // {{{ analyze
    function analyze($input)
    {
        // Initializing
        $this->lexer->init($input, $this->_symbolTable);

        // Set look ahead
        $this->lookAhead = $this->lexer->nextToken();
    }
    // }}}
    // {{{ getLookAhead
    function getLookAhead()
    {
        return $this->lookAhead;
    }
    // }}}
    // {{{ getLookAheadVal
    function getLookAheadVal()
    {
        return $this->lexer->getTokenVal();
    }
    // }}}
    // {{{ _match
    function _match($token)
    {
    }
    // }}}
    // {{{ _tokenToString
    function _tokenToString($token , $tokenVal)
    {
        return $token;
    }
    // }}}
    // {{{ hasErrors
    function hasErrors()
    {
        return $this->stack->hasErrors();
    }
    // }}}
    // {{{ & getErrors
    function & getErrors()
    {
        // XXX: Use additional variable to prevent 'Only variables should be assigned by reference' notice
        $errors = $this->stack->getErrors();
        return $errors;
    }
    // }}}
}
