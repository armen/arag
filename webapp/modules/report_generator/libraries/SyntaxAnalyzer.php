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
    protected $stack        = Null;   // Error stack
    public    $symbolTable  = Null;

    const SYNTAX_ERROR       = 100;
    const UNDEFINED_ID_ERROR = 101;

    // }}}
    // {{{ constructor
    public function __construct($lexer)
    {
        // Set lexical analyzer object
        $this->lexer = $lexer;

        // Set error stack
        $this->stack = new ErrorStack;
    }
    // }}}
    // {{{ analyze
    public function analyze($input)
    {
        // Initializing
        $this->lexer->init($input, $this->symbolTable);

        // Set look ahead
        $this->lookAhead = $this->lexer->nextToken();
    }
    // }}}
    // {{{ getLookAhead
    public function getLookAhead()
    {
        return $this->lookAhead;
    }
    // }}}
    // {{{ getLookAheadVal
    public function getLookAheadVal()
    {
        return $this->lexer->getTokenVal();
    }
    // }}}
    // {{{ match
    protected function match($token)
    {
    }
    // }}}
    // {{{ tokenToString
    protected function tokenToString($token , $tokenVal)
    {
        return $token;
    }
    // }}}
    // {{{ hasErrors
    public function hasErrors()
    {
        return $this->stack->hasErrors();
    }
    // }}}
    // {{{ getErrors
    public function getErrors()
    {
        return $this->stack->getErrors();
    }
    // }}}
}
