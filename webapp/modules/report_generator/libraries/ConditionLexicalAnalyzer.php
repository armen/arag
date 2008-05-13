<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class ConditionLexicalAnalyzer extends LexicalAnalyzer
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
                               // XXX: ConditionLexicalAnalyzer::T_RELOP must be after _NE,_GE,
                               //      _LE because those contain <,=,>
    const T_WITESPACE = 9; 
    const T_INVALID   = 10;
    const T_OPERATOR  = 998;   // NOT, OR, AND
    const T_FUNCTION  = 999;
    const T_EOI       = 1000;
    
    // }}}
    // {{{ constructor
    public function __contsruct()
    {
        // Set symbol table
        $this->symbolTable = new SymbolTable;        

        $this->addTokenPatterns(ConditionLexicalAnalyzer::T_ID,        "[a-zA-z][0-9a-zA-Z_]+");  // functions and columns id
        $this->addTokenPatterns(ConditionLexicalAnalyzer::T_NUMBER,    "[0-9]\.?[0-9]*");         // numbers decimal or float numbers
        $this->addTokenPatterns(ConditionLexicalAnalyzer::T_VALUE,     "'[^']*'");                // values XXX: this is very MySQL specific :(
        $this->addTokenPatterns(ConditionLexicalAnalyzer::T_PARENTHES, "[\(\)]");
        $this->addTokenPatterns(ConditionLexicalAnalyzer::T_RELOP_LE,  "<=");
        $this->addTokenPatterns(ConditionLexicalAnalyzer::T_RELOP_GE,  ">=");
        $this->addTokenPatterns(ConditionLexicalAnalyzer::T_RELOP_NE,  "!=");
        $this->addTokenPatterns(ConditionLexicalAnalyzer::T_RELOP,     "[<>=]");
        $this->addTokenPatterns(ConditionLexicalAnalyzer::T_WITESPACE, "\s\s*");
        $this->addTokenPatterns(ConditionLexicalAnalyzer::T_INVALID,   "[^\s]");

        $this->addSkipToken(ConditionLexicalAnalyzer::T_WITESPACE);
        $this->setEOIToken(ConditionLexicalAnalyzer::T_EOI);

        // Set case insensitive check for symboltable
        $this->symbolTable->setCaseSensitiveCheck(False);
        
        // Add pre defined id to symbol table and it's token
        $this->symbolTable->insert('and', ConditionLexicalAnalyzer::T_OPERATOR);
        $this->symbolTable->insert('or', ConditionLexicalAnalyzer::T_OPERATOR);
        // $this->symbolTable->insert('not', ConditionLexicalAnalyzer::T_OPERATOR);
    }
    // }}}
    // {{{ & nextToken
    public function & nextToken()
    {
        $token = parent::nextToken();

        // If token was a ID then it could be a column name, function or a operator
        if ($token == ConditionLexicalAnalyzer::T_ID) {
            if (($sToken = $this->symbolTable->search($this->getPrevTokenVal())) != Null) {
                $token = $sToken;
            }
        } else if ($token == ConditionLexicalAnalyzer::T_PARENTHES) {
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
