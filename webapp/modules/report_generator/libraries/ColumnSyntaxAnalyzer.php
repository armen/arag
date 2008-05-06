<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once OSC_WEBAPP_PATH . '/engine/core/OSC_ErrorStack.class.php';

// {{{ Error codes

define ('SA_ERROR_SYNTAX_ERROR', 100);
define ('SA_ERROR_UNDEFINED_ID', 101);

// }}}

class ColumnSyntaxAnalyzer extends SyntaxAnalyzer
{
    // {{{ properties
    
    var $lookAhead    = Null;
    var $symbolTable  = Null;

    // }}}
    // {{{ Constructor
    public function ColumnSyntaxAnalyzer()
    {
        $controller =& Controller::getInstance();
        $lexer      =& $controller->getModel('ColumnLexicalAnalyzer', 'RG');

        // Set symbol table
        $this->symbolTable = $lexer->getSymbolTable();

        // Create error stack
        $stack =& OSC_ErrorStack::singleton('ColumnSyntaxAnalyzer');

        // Call parent constructor
        parent::SyntaxAnalyzer($lexer, $stack);

        // Set Error messages
        $this->setErrorMessages();
    }
    // }}}
    // {{{ analyze
    public function analyze($input)
    {
        parent::analyze(trim(preg_replace('/\s+/', ' ', $input)));

        // Call the grammer
        $this->_stmt();
    }
    // }}}
    // {{{ _match
    protected function match($token)
    {
        // DEBUGGING:
        // echo $this->_tokenToString($token , $token) . ' ';

        if ($token == RG_T_ID) {
            if ($this->symbolTable->search($this->lexer->getPrevTokenVal()) == Null) {

                // Push error to error stack
                $input  = $this->lexer->getInput();
                $id     = $this->lexer->getPrevTokenVal();
                $offset = strpos($input, $id);

                $splitedInput = Array ('first_part'   => substr($input, 0, $offset), 
                                       'unknown_part' => substr($input, $offset, strlen($id)), 
                                       'last_part'    => substr($input, $offset+strlen($id)));

                $params = Array('id' => $id, 'splitedinput' => $splitedInput); 
                $this->stack->push(SA_ERROR_UNDEFINED_ID, 'error', $params, 'Undifined ID');
            }
        }
        
        if ($this->getLookAhead() == $token) {
            $this->lookAhead = $this->lexer->nextToken();
        } else {
            
            // Push error to error stack
            $input  = $this->lexer->getInput();
            $offset = $this->lexer->getInputOffset();
            $match  = $this->_tokenToString($token, $this->lexer->getTokenVal());
            $tToken = $this->_tokenToString($this->lexer->prevToken(),$this->lexer->getPrevTokenVal());
            $splitedInput = Array ('first_part'   => substr($input, 0, $offset-strlen($tToken)), 
                                   'unknown_part' => substr($input, $offset-strlen($tToken), strlen($tToken)),
                                   'last_part'    => substr($input, $offset, strlen($input)));
            
            $params = Array ('token' => $tToken, 'match' => $match, 'offset'=> $offset, 'splitedinput' => $splitedInput);

            $this->stack->push(SA_ERROR_SYNTAX_ERROR, 'error', $params, 'Syntax Error');
        }
    }
    // }}}
    // {{{ _tokenToString
    protected function tokenToString($token , $tokenVal)
    {
        switch ($token) {
            case RG_T_ID:       $stredToken = (trim($tokenVal))?$tokenVal:'ID'; break;
            case RG_T_NUMBER:   $stredToken = (trim($tokenVal))?$tokenVal:'NUMBER'; break;
            case RG_T_FUNCTION: $stredToken = (trim($tokenVal))?$tokenVal:'FUNCTION'; break;
            case RG_T_EOI:      $stredToken = 'EOI'; break;
            case '-': case '+': case '*':
            case '/': case '%': case '(': case ')':
                $stredToken = $token; break;
            default:
                $stredToken = $tokenVal;
        }

        return $stredToken;
    }
    // }}}
    // {{{ Grammer
    protected function stmt() { $this->_mathStmt(); $this->_match(RG_T_EOI); }
    protected function mathStmt()
    {
        if ($this->getLookAhead() == RG_T_ID || $this->getLookAhead() == RG_T_NUMBER) {
        
            $this->_match($this->getLookAhead());
        
        } else if ($this->getLookAhead() == '(') {
            
            $this->_match('('); $this->_mathStmt(); $this->_match(')');
        
        } else if ($this->getLookAhead() == RG_T_FUNCTION) {
            
            $this->_match(RG_T_FUNCTION); $this->_match('('); $this->_match(RG_T_ID); $this->_match(')'); 
        
        } else {

            // Push error to error stack
            $input        = $this->lexer->getInput();
            $offset       = $this->lexer->getInputOffset();
            $tToken       = $this->_tokenToString($this->lookAhead, $this->lexer->getTokenVal());
            $splitedInput = Array ('first_part'   => substr($input, 0, $offset-strlen($tToken)), 
                                   'unknown_part' => substr($input, $offset-strlen($tToken), strlen($tToken)),
                                   'last_part'    => substr($input, $offset, strlen($input)));
            
            $params = Array ('token' => $tToken, 'match' => "(', `ID' or `NUMBER", 'offset' => $offset,
                             'splitedinput' => $splitedInput);

            $this->stack->push(SA_ERROR_SYNTAX_ERROR, 'error', $params, 'Syntax Error');
        }

        // After every _mathStmt we should have _mathStmtSecPart
        $this->_mathStmtSecPart();
    }
    protected function mathStmtSecPart()
    {
        if ($this->getLookAhead() == '+' || $this->getLookAhead() == '-' || 
            $this->getLookAhead() == '*' || $this->getLookAhead() == '/' || 
            $this->getLookAhead() == '%') {
            
            $this->_match($this->getLookAhead()); $this->_mathStmt();
        }
    }
    // }}}
    // {{{ setErrorMessages
    public function setErrorMessages()
    {
        $messages = Array(
            SA_ERROR_SYNTAX_ERROR => "Syntax error near `%token%' token. I can't match `%match%' in offset: %offset%",
            SA_ERROR_UNDEFINED_ID => "Undefined ID '%id%'"
        );
                          
        $this->stack->setErrorMessageTemplate($messages);
    }
    // }}}
}

// {{{ Tests
// osc_text_4 COUNT(osc_text_3)
// COUNT(osc_text_3) osc_text_4
// 1
// 1+1
// osc_text_4
// COUNT(osc_text_3)
// (1+1)
// 1 + (1+count(osc_text_4)+2) / 22 + (osc_text_4)-osc_text_4
// 1 + (1+count(osc_text_4)+2) * 22 + (osc_text_4)-osc_text_4
// osc_textarea_9 + 0.2 - 22   <--- float number test
// osc_textarea_9 + 0.2 - .22  <--- error
// osc_textarea_9 + 0.2 - 2.2.
// }}}
