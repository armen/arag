<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

// {{{ Tokens

define ('RG_T_ID',        1);
define ('RG_T_NUMBER',    2);
define ('RG_T_VALUE',     3);
define ('RG_T_PARENTHES', 4);     // (, )
define ('RG_T_RELOP_NE',  5);     // !=
define ('RG_T_RELOP_GE',  6);     // >=
define ('RG_T_RELOP_LE',  7);     // <=
define ('RG_T_RELOP',     8);     // <, =, >  
                                  // XXX: RG_T_RELOP must be after _NE,_GE,
                                  //      _LE because those contain <,=,>
define ('RG_T_WITESPACE', 9); 
define ('RG_T_INVALID',   10);
define ('RG_T_OPERATOR',  998);   // NOT, OR, AND
define ('RG_T_FUNCTION',  999);
define ('RG_T_EOI',       1000);

// }}}

class ConditionLexicalAnalyzer extends LexicalAnalyzer
{
    // {{{ constructor
    function ConditionLexicalAnalyzer()
    {
        $controller         =& Controller::getInstance();
        $this->_symbolTable =& $controller->getModel('SymbolTable', 'RG');

        $this->addTokenPatterns(RG_T_ID,        "[a-zA-z][0-9a-zA-Z_]+");  // functions and columns id
        $this->addTokenPatterns(RG_T_NUMBER,    "[0-9]\.?[0-9]*");         // numbers decimal or float numbers
        $this->addTokenPatterns(RG_T_VALUE,     "'[^']*'");                // values XXX: this is very MySQL specific :(
        $this->addTokenPatterns(RG_T_PARENTHES, "[\(\)]");
        $this->addTokenPatterns(RG_T_RELOP_LE,  "<=");
        $this->addTokenPatterns(RG_T_RELOP_GE,  ">=");
        $this->addTokenPatterns(RG_T_RELOP_NE,  "!=");
        $this->addTokenPatterns(RG_T_RELOP,     "[<>=]");
        $this->addTokenPatterns(RG_T_WITESPACE, "\s\s*");
        $this->addTokenPatterns(RG_T_INVALID,   "[^\s]");

        $this->addSkipToken(RG_T_WITESPACE);
        $this->setEOIToken(RG_T_EOI);

        // Set case insensitive check for symboltable
        $this->_symbolTable->setCaseSensitiveCheck(False);
        
        // Add pre defined id to symbol table and it's token
        $this->_symbolTable->insert('and', RG_T_OPERATOR);
        $this->_symbolTable->insert('or', RG_T_OPERATOR);
        // $this->_symbolTable->insert('not', RG_T_OPERATOR);
    }
    // }}}
    // {{{ & nextToken
    function & nextToken()
    {
        $token = parent::nextToken();

        // If token was a ID then it could be a column name, function or a operator
        if ($token == RG_T_ID) {
            if (($sToken = $this->_symbolTable->search($this->getPrevTokenVal())) != Null) {
                $token = $sToken;
            }
        } else if ($token == RG_T_PARENTHES) {
            $token = $this->getPrevTokenVal();
        }        

        return $token;
    }
    // }}}
    // {{{ & getSymbolTable
    function & getSymbolTable()
    {
        return $this->_symbolTable;
    }
    // }}}
}
