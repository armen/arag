<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class ColumnLexicalAnalyzer extends LexicalAnalyzer
{
    // {{{ properties

    const T_ID        = 1;
    const T_NUMBER    = 2;
    const T_OPERATOR  = 3;
    const T_WITESPACE = 4; 
    const T_INVALID   = 5;
    const T_FUNCTION  = 999;
    const T_EOI       = 1000;

    // }}}
    // {{{ constructor
    public function __construct()
    {
        // Set symbol table
        $this->symbolTable = new SymbolTable;
       
        // Add patterns for matching 
        $this->addTokenPatterns(ColumnLexicalAnalyzer::T_ID,        "[a-zA-z][0-9a-zA-Z_]+");  // functions and columns id
        $this->addTokenPatterns(ColumnLexicalAnalyzer::T_NUMBER,    "[0-9]\.?[0-9]*");         // numbers
        $this->addTokenPatterns(ColumnLexicalAnalyzer::T_OPERATOR,  "[*\/\%\-+\(\)]");         // *, /, %, -, +, ( and )
        $this->addTokenPatterns(ColumnLexicalAnalyzer::T_WITESPACE, "\s\s*");
        $this->addTokenPatterns(ColumnLexicalAnalyzer::T_INVALID,   "[^\s]");

        $this->addSkipToken(ColumnLexicalAnalyzer::T_WITESPACE);
        $this->setEOIToken(ColumnLexicalAnalyzer::T_EOI);

        // Set case insensitive check for symboltable
        $this->symbolTable->setCaseSensitiveCheck(False);

        // Add pre defined id to symbol table and it's token
        //$this->symbolTable->insert('count', ColumnLexicalAnalyzer::T_FUNCTION);
        //$this->symbolTable->insert('sum', ColumnLexicalAnalyzer::T_FUNCTION);
    }
    // }}}
    // {{{ nextToken
    public function nextToken()
    {
        $token = parent::nextToken();

        // If token was a ID then it could be a column name or a function
        if ($token == ColumnLexicalAnalyzer::T_ID) {
            if (($sToken = $this->symbolTable->search($this->getPrevTokenVal())) != Null) {
                $token = $sToken;
            }
        } else if ($token == ColumnLexicalAnalyzer::T_OPERATOR) {
            $token = $this->getPrevTokenVal();
        }

        return $token;
    }
    // }}}
    // {{{ getSymbolTable
    public function getSymbolTable()
    {
        return $this->symbolTable;
    }
    // }}}
}
