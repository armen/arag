<?php

class LexicalAnalyzer
{
    // {{{ properties
    
    var $tokens       = Array();
    var $currentToken = Null;
    var $prevToken    = Null;
    var $inputLength  = 0;
    var $inputOffset  = 0;
    var $symbolTable  = Null;
    
    var $patterns     = Array();
    var $skipTokens   = Array();
    var $EOIToken     = Null;

    // }}}
    // {{{ constructor
    public function __construct()
    {
    }
    // }}}
    // {{{ & init
    public function & init($input, &$symbolTable)
    {
        $this->input = $input;
        
        // Set symboltable
        $this->symbolTable =& $symbolTable;
        
        // Set input length
        $this->inputLength = strlen($input);

        // Reset index for nextToken and prevToken
        $this->currentToken = 0;
        $this->prevToken    = 0;
        
        // Get pattern from patterns array
        $pattern = $this->getPattern();

        // Match all tokens
        preg_match_all($pattern, $input, $matchs);

        //echo "<pre>";
        //print_r($matchs);
        //echo "</pre>";
        
        // Get all tokens
        $tokens = $matchs[0];
        unset($matchs[0]);

        // Get categorized tokens list
        $tokensList = $matchs;

        foreach ($tokens as $offset => $value) {
        
            foreach ($tokensList as $token => $dummy) {
                if ($tokensList[$token][$offset] != Null) {
                    
                    $this->tokens[$offset] = Array('token' => $token, 'token_val' => $value);
                }
            }
        }

        return $this->tokens;
    }
    // }}}
    // {{{ getPattern
    public function getPattern()
    {
        if (count($this->patterns) == 0) {
            return "//";
        }
        
        $pattern = "/";
        reset($this->patterns);
        ksort($this->patterns);
        $index = 1;
        while ($regex = current($this->patterns)) {
            
            if (key($this->patterns) > $index++) {
                $pattern .= "()|";
            } else {            
                $pattern .= (next($this->patterns))?"($regex)|":"($regex)/i";
            }    
        }

        return $pattern;
    }
    // }}}
    // {{{ & nextToken
    public function & nextToken()
    {
        if ($this->currentToken === Null && !count($this->tokens)) {
            // Error: please call init first
            return False;
        }

        // escape unset offsets
        while(($this->currentToken <= $this->inputLength) && 
              (!isset($this->tokens[$this->currentToken]))) {
              
              $this->currentToken++;
        }

        if (isset($this->tokens[$this->currentToken])) {

            $this->inputOffset += strlen($this->tokens[$this->currentToken]['token_val']);

            // Skip unwanted tokens
            if (in_array($this->tokens[$this->currentToken]['token'], $this->skipTokens)) {
                $this->currentToken++;
                return $this->nextToken();
            }
            
            $this->prevToken = $this->currentToken;
            $token            = $this->tokens[$this->currentToken++]['token'];

            return $token;
        }    
        
        return $this->EOIToken;
    }
    // }}}
    // {{{ addTokenPatterns
    public function addTokenPatterns($token, $pattern) 
    {
        $this->patterns[$token] = $pattern;
    }
    // }}}
    // {{{ addSkipToken
    public function addSkipToken($token)
    {
        $this->skipTokens[] = $token;
    }
    // }}}
    // {{{ setEOIToken
    public function setEOIToken($token)
    {
        $this->EOIToken = $token;
    }
    // }}}
    // {{{ getTokenVal
    public function getTokenVal()
    {
        if (isset($this->tokens[$this->currentToken]['token_val'])) {
            return $this->tokens[$this->currentToken]['token_val'];
        }

        return Null;
    }
    // }}}
    // {{{ & prevToken
    public function & prevToken()
    {
        if (isset($this->tokens[$this->prevToken]['token'])) {
            return $this->tokens[$this->prevToken]['token'];
        }

        return Null;
    }
    // }}}
    // {{{ getPrevTokenVal
    public function getPrevTokenVal()
    {
        if (isset($this->tokens[$this->prevToken]['token_val'])) {
            return $this->tokens[$this->prevToken]['token_val'];
        }

        return Null;
    }
    // }}}
    // {{{ setPrevTokenVal
    public function setPrevTokenVal($tokenVal)
    {
        if (isset($this->tokens[$this->prevToken])) {
            $this->tokens[$this->prevToken]['token_val'] = $tokenVal;
        }

        return Null;
    }
    // }}}
    // {{{ getInputOffset
    public function getInputOffset()
    {
        return $this->inputOffset;
    }
    // }}}
    // {{{ getInput
    public function getInput()
    {
        return $this->input;
    }
    // }}}
}
