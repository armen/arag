<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class FilterLexicalAnalyzer extends LexicalAnalyzer
{
    // {{{ properties

    const T_ID        = 1;
    const T_NUMBER    = 2;
    const T_VALUE     = 3;
    const T_PARENTHES = 4;     // (, )
    const T_RELOP_NE  = 5;     // !=
    const T_RELOP_GE  = 6;     // >=
    const T_RELOP_LE  = 7;     // <=
    const T_RELOP     = 8;     // <, =, >  
                               // XXX: FilterLexicalAnalyzer::T_RELOP must be after _NE,_GE,
                               //      _LE because those contain <,=,>
    const T_WITESPACE = 9; 
    const T_INVALID   = 10;
    const T_OPERATOR  = 998;   // NOT, OR, AND
    const T_FUNCTION  = 999;
    const T_EOI       = 1000;
    
    // }}}
    // {{{ constructor
    public function __construct()
    {
        // Set symbol table
        $this->symbolTable = new SymbolTable;        

        $this->addTokenPatterns(FilterLexicalAnalyzer::T_ID,        "[a-zA-z][0-9a-zA-Z_]+");  // functions and columns id
        $this->addTokenPatterns(FilterLexicalAnalyzer::T_NUMBER,    "[0-9]\.?[0-9]*");         // numbers decimal or float numbers
        $this->addTokenPatterns(FilterLexicalAnalyzer::T_VALUE,     "'[^']*'");                // values XXX: this is very MySQL specific :(
        $this->addTokenPatterns(FilterLexicalAnalyzer::T_PARENTHES, "[\(\)]");
        $this->addTokenPatterns(FilterLexicalAnalyzer::T_RELOP_LE,  "<=");
        $this->addTokenPatterns(FilterLexicalAnalyzer::T_RELOP_GE,  ">=");
        $this->addTokenPatterns(FilterLexicalAnalyzer::T_RELOP_NE,  "!=");
        $this->addTokenPatterns(FilterLexicalAnalyzer::T_RELOP,     "[<>=]");
        $this->addTokenPatterns(FilterLexicalAnalyzer::T_WITESPACE, "\s\s*");
        $this->addTokenPatterns(FilterLexicalAnalyzer::T_INVALID,   "[^\s]");

        $this->addSkipToken(FilterLexicalAnalyzer::T_WITESPACE);
        $this->setEOIToken(FilterLexicalAnalyzer::T_EOI);

        // Set case insensitive check for symboltable
        $this->symbolTable->setCaseSensitiveCheck(False);
        
        // Add pre defined id to symbol table and it's token
        $this->symbolTable->insert('and', FilterLexicalAnalyzer::T_OPERATOR);
        $this->symbolTable->insert('or', FilterLexicalAnalyzer::T_OPERATOR);
        // $this->symbolTable->insert('not', FilterLexicalAnalyzer::T_OPERATOR);
    }
    // }}}
    // {{{ & nextToken
    public function & nextToken()
    {
        $token = parent::nextToken();

        // If token was a ID then it could be a column name, function or a operator
        if ($token == FilterLexicalAnalyzer::T_ID) {
            if (($sToken = $this->symbolTable->search($this->getPrevTokenVal())) != Null) {
                $token = $sToken;
            }
        } else if ($token == FilterLexicalAnalyzer::T_PARENTHES) {
            $token = $this->getPrevTokenVal();
        }        

        return $token;
    }
    // }}}
    // {{{ & getSymbolTable
    public function & getSymbolTable()
    {
        return $this->symbolTable;
    }
    // }}}
}
