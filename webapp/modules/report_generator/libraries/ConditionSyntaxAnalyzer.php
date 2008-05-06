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

class ConditionSyntaxAnalyzer extends SyntaxAnalyzer
{
    // {{{ properties
    
    var $lookAhead    = Null;
    var $_symbolTable  = Null;

    // }}}
    // {{{ Constructor
    function ConditionSyntaxAnalyzer()
    {
        $controller =& Controller::getInstance();
        $lexer      =& $controller->getModel('ConditionLexicalAnalyzer', 'RG');

        // Set symbol table
        $this->_symbolTable = $lexer->getSymbolTable();

        // Create error stack
        $stack =& OSC_ErrorStack::singleton('ConditionSyntaxAnalyzer');

        // Call parent constructor
        parent::SyntaxAnalyzer($lexer, $stack);

        // Set Error messages
        $this->setErrorMessages();
    }
    // }}}
    // {{{ analyze
    function analyze($input)
    {
        parent::analyze(trim(preg_replace('/\s+/', ' ', $input)));

        // Call the grammer
        $this->_conditions();
    }
    // }}}
    // {{{ _match
    function _match($token)
    {
        // DEBUGGING:
        // echo $this->_tokenToString($token , $token) . ' ';

        if ($token == RG_T_ID) {
            if ($this->_symbolTable->search($this->lexer->getPrevTokenVal()) == Null) {
                
                // Push error to error stack
                $input  = $this->lexer->getInput();
                $id     = $this->lexer->getPrevTokenVal();
                $offset = strpos($input, $id);

                $splitedInput = Array ('first_part'   => substr($input, 0, $offset), 
                                       'unknown_part' => substr($input, $offset, strlen($id)), 
                                       'last_part'    => substr($input, $offset+strlen($id)));

                $params = Array('id' => $id, 'splited_input' => $splitedInput); 
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

            $params = Array ('token' => $tToken, 'match' => $match, 'offset'=> $offset, 'splited_input' => $splitedInput);

            $this->stack->push(SA_ERROR_SYNTAX_ERROR, 'error', $params, 'Syntax Error');
        }
    }
    // }}}
    // {{{ _tokenToString
    function _tokenToString($token , $tokenVal)
    {
        switch ($token) {
            case RG_T_ID:       $stredToken = (trim($tokenVal))?$tokenVal:'ID'; break;
            case RG_T_NUMBER:   $stredToken = (trim($tokenVal))?$tokenVal:'NUMBER'; break;
            case RG_T_FUNCTION: $stredToken = (trim($tokenVal))?$tokenVal:'FUNCTION'; break;
            case RG_T_OPERATOR: $stredToken = (trim($tokenVal))?$tokenVal:'OPERATOR'; break;
            case RG_T_VALUE:    $stredToken = (trim($tokenVal))?$tokenVal:'VALUE'; break;
            case RG_T_EOI:      $stredToken = 'EOI'; break;
            
            case '(':
            case ')':
                $stredToken = $token;
                break;

            case RG_T_RELOP: 
            case RG_T_RELOP_NE: 
            case RG_T_RELOP_GE: 
            case RG_T_RELOP_LE:
                $stredToken = (trim($tokenVal))?$tokenVal:'RELOP'; 
                break;

            default:
                $stredToken = $tokenVal;
        }

        return $stredToken;
    }
    // }}}
    // {{{ Grammer
    function _conditions() 
    {        
        $this->_condition();
        $this->_match(RG_T_EOI);
    }
    function _condition() 
    {
        // XXX: every condition starts with NUMBER,ID,VALUE or ( and continues with _conditionPart()
        
        if ($this->getLookAhead() == RG_T_ID || $this->getLookAhead() == RG_T_NUMBER ||
            $this->getLookAhead() == RG_T_VALUE) {

            $this->_match($this->getLookAhead());
            $this->_conditionPart();

        } else if ($this->getLookAhead() == '(') {
        
            $this->_match('('); $this->_condition(); $this->_match(')'); $this->_conditionPart();
        }        
    }
    function _conditionPart()
    {
        // XXX: This function matchs:
        //      
        //      (RG_T_RELOP* | RG_T_OPERATOR) RG_T_ID etc,...
        
        if ($this->getLookAhead() == RG_T_RELOP || $this->getLookAhead() == RG_T_RELOP_NE || 
            $this->getLookAhead() == RG_T_RELOP_GE || $this->getLookAhead() == RG_T_RELOP_LE ||
            $this->getLookAhead() == RG_T_OPERATOR) {
        
            $this->_match($this->getLookAhead());
            
            if ($this->getLookAhead() == RG_T_ID || $this->getLookAhead() == RG_T_NUMBER ||
                $this->getLookAhead() == RG_T_VALUE) {
                
                $this->_match($this->getLookAhead());

            } else if ($this->getLookAhead() == '(') {
                
                $this->_condition();
            } else {
                // _conditionParts matchs a ID, NUMBER, VALUE or ( but not matchs a RELOP
                // after that. we can't match a ID, NUMBER, VALUE or (
                // Push error to error stack
                $input        = $this->lexer->getInput();
                $offset       = $this->lexer->getInputOffset();
                $tToken       = $this->_tokenToString($this->getLookAhead(), $this->lexer->getTokenVal());
                $splitedInput = Array ('first_part'   => substr($input, 0, $offset-strlen($tToken)), 
                                       'unknown_part' => substr($input, $offset-strlen($tToken), strlen($tToken)),
                                       'last_part'    => substr($input, $offset, strlen($input)));
            
                $params = Array ('token' => $tToken, 'match' => "ID, NUMBER, VALUE or (", 'offset' => $offset,
                                 'splited_input' => $splitedInput);

                $this->stack->push(SA_ERROR_SYNTAX_ERROR, 'error', $params, 'Syntax Error');
            }

            $this->_conditionPart();
        }            
    }
    // }}}
    // {{{ setErrorMessages
    function setErrorMessages()
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
// osc_text_3 = '2' AND osc_text_4 = 'hi2'
// (osc_text_3 = '2') AND (osc_text_4 = 'hi2')
// osc_text_3 AND osc_text_4
// (osc_text_3 AND '2') AND (osc_text_4 = 'hi2')
// osc_text_3 AND osc_text_3 AND osc_text_4
// osc_text_3 = '2' AND osc_text_4 <= 'hi2' >= 2 < 4 > 3 != 4
// (((osc_text_3 = '2') AND osc_text_4) <= 'hi2' >= (2 < 4) > (3 != 4))
// '2' AND osc_text_4 = 'hi2'
// ------Error------
// (((osc_text_3 = '2') AND osc_text_4) <= 'hi2' >= (2 < 4) > (3) != 4
// osc_text_3 osc_text_4
// osc_text_4 '4'
// }}}
