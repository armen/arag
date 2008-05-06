<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class SymbolTable
{
    // {{{ properties
    
    var $_symbols            = Array();
    var $_caseSensitiveCheck = False;
    
    // }}}
    // {{{ insert
    function insert($lexeme, $token)
    {
        if (!$this->_caseSensitiveCheck) {
            // Change lexeme to lower case to test in case insesitive mode
            $lexeme = strtolower($lexeme);
        }
        
        $this->_symbols[$lexeme] = $token;
    }
    // }}}
    // {{{ search
    function search($lexeme)
    {
        if (!$this->_caseSensitiveCheck) {
            // Change lexeme to lower case to test in case insesitive mode        
            $lexeme = strtolower($lexeme);
        }

        if (isset($this->_symbols[$lexeme])) {
            return $this->_symbols[$lexeme];
        }

        return Null;
    }
    // }}}
    // {{{ setCaseSensitiveCheck
    function setCaseSensitiveCheck($caseSensitiveCheck)
    {
        $this->_caseSensitiveCheck = $caseSensitiveCheck;
    }
    // }}}
};

?>
