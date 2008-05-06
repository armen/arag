<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

define ('RG_T_ID',        1);
define ('RG_T_NUMBER',    2);
define ('RG_T_OPERATOR',  3);
define ('RG_T_WITESPACE', 4); 
define ('RG_T_INVALID',   5);
define ('RG_T_FUNCTION',  999);
define ('RG_T_EOI',       1000);

class ColumnLexicalAnalyzer extends LexicalAnalyzer
{
    // {{{ constructor
    public function __construct()
    {
        $controller  =& Controller::getInstance();
        $symbolTable =& $controller->getModel('SymbolTable', 'RG');

        // Set symbol table
        $this->symbolTable =& $symbolTable;
       
        // Add patterns for matching 
        $this->addTokenPatterns(RG_T_ID,        "[a-zA-z][0-9a-zA-Z_]+");  // functions and columns id
        $this->addTokenPatterns(RG_T_NUMBER,    "[0-9]\.?[0-9]*");         // numbers
        $this->addTokenPatterns(RG_T_OPERATOR,  "[*\/\%\-+\(\)]");         // *, /, %, -, +, ( and )
        $this->addTokenPatterns(RG_T_WITESPACE, "\s\s*");
        $this->addTokenPatterns(RG_T_INVALID,   "[^\s]");

        $this->addSkipToken(RG_T_WITESPACE);
        $this->setEOIToken(RG_T_EOI);

        // Set case insensitive check for symboltable
        $this->symbolTable->setCaseSensitiveCheck(False);

        // Add pre defined id to symbol table and it's token
        //$this->symbolTable->insert('count', RG_T_FUNCTION);
        //$this->symbolTable->insert('sum', RG_T_FUNCTION);
    }
    // }}}
    // {{{ & nextToken
    public function & nextToken()
    {
        $token = parent::nextToken();

        // If token was a ID then it could be a column name or a function
        if ($token == RG_T_ID) {
            if (($sToken = $this->symbolTable->search($this->getPrevTokenVal())) != Null) {
                $token = $sToken;
            }
        } else if ($token == RG_T_OPERATOR) {
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
