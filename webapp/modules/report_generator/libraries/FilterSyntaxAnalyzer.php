<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class FilterSyntaxAnalyzer extends SyntaxAnalyzer
{
    // {{{ Constructor
    public function __construct()
    {
        $lexer = new FilterLexicalAnalyzer;

        // Set symbol table
        $this->symbolTable = $lexer->getSymbolTable();

        parent::__construct(new FilterLexicalAnalyzer);

        // Set Error messages
        $this->setErrorMessages();
    }
    // }}}
    // {{{ analyze
    public function analyze($input)
    {
        parent::analyze(trim(preg_replace('/\s+/', ' ', $input)));

        // Call the grammer
        $this->conditions();
    }
    // }}}
    // {{{ _match
    protected function match($token)
    {
        // DEBUGGING:
        // echo $this->tokenToString($token , $token) . ' ';

        if ($token == FilterLexicalAnalyzer::T_ID) {
            if ($this->symbolTable->search($this->lexer->getPrevTokenVal()) == Null) {
                
                // Push error to error stack
                $input  = $this->lexer->getInput();
                $id     = $this->lexer->getPrevTokenVal();
                $offset = strpos($input, $id);

                $splitedInput = Array ('first_part'   => substr($input, 0, $offset), 
                                       'unknown_part' => substr($input, $offset, strlen($id)), 
                                       'last_part'    => substr($input, $offset+strlen($id)));

                $params = Array('id' => $id, 'splitedinput' => $splitedInput); 
                $this->stack->push(SyntaxAnalyzer::UNDEFINED_ID_ERROR, 'error', $params, 'Undifined ID');
            }
        }
        
        if ($this->getLookAhead() == $token) {
            $this->lookAhead = $this->lexer->nextToken();
        } else {

            // Push error to error stack
            $input  = $this->lexer->getInput();
            $offset = $this->lexer->getInputOffset();
            $match  = $this->tokenToString($token, $this->lexer->getTokenVal());
            $tToken = $this->tokenToString($this->lexer->prevToken(),$this->lexer->getPrevTokenVal());
            $splitedInput = Array ('first_part'   => substr($input, 0, $offset-strlen($tToken)), 
                                   'unknown_part' => substr($input, $offset-strlen($tToken), strlen($tToken)),
                                   'last_part'    => substr($input, $offset, strlen($input)));

            $params = Array ('token' => $tToken, 'match' => $match, 'offset'=> $offset, 'splitedinput' => $splitedInput);

            $this->stack->push(SyntaxAnalyzer::SYNTAX_ERROR, 'error', $params, 'Syntax Error');
        }
    }
    // }}}
    // {{{ _tokenToString
    protected function tokenToString($token , $tokenVal)
    {
        switch ($token) {
            case FilterLexicalAnalyzer::T_ID:       $stredToken = (trim($tokenVal))?$tokenVal:'ID'; break;
            case FilterLexicalAnalyzer::T_NUMBER:   $stredToken = (trim($tokenVal))?$tokenVal:'NUMBER'; break;
            case FilterLexicalAnalyzer::T_FUNCTION: $stredToken = (trim($tokenVal))?$tokenVal:'FUNCTION'; break;
            case FilterLexicalAnalyzer::T_OPERATOR: $stredToken = (trim($tokenVal))?$tokenVal:'OPERATOR'; break;
            case FilterLexicalAnalyzer::T_VALUE:    $stredToken = (trim($tokenVal))?$tokenVal:'VALUE'; break;
            case FilterLexicalAnalyzer::T_EOI:      $stredToken = 'EOI'; break;
            
            case '(':
            case ')':
                $stredToken = $token;
                break;

            case FilterLexicalAnalyzer::T_RELOP: 
            case FilterLexicalAnalyzer::T_RELOP_NE: 
            case FilterLexicalAnalyzer::T_RELOP_GE: 
            case FilterLexicalAnalyzer::T_RELOP_LE:
                $stredToken = (trim($tokenVal))?$tokenVal:'RELOP'; 
                break;

            default:
                $stredToken = $tokenVal;
        }

        return $stredToken;
    }
    // }}}
    // {{{ Grammer
    protected function conditions() 
    {        
        $this->condition();
        $this->match(FilterLexicalAnalyzer::T_EOI);
    }
    protected function condition() 
    {
        // XXX: every condition starts with NUMBER,ID,VALUE or ( and continues with _conditionPart()
        
        if ($this->getLookAhead() == FilterLexicalAnalyzer::T_ID || $this->getLookAhead() == FilterLexicalAnalyzer::T_NUMBER ||
            $this->getLookAhead() == FilterLexicalAnalyzer::T_VALUE) {

            $this->match($this->getLookAhead());
            $this->conditionPart();

        } else if ($this->getLookAhead() == '(') {
        
            $this->match('('); $this->condition(); $this->match(')'); $this->conditionPart();
        }        
    }
    protected function conditionPart()
    {
        // XXX: This function matchs:
        //      
        //      (FilterLexicalAnalyzer::T_RELOP* | FilterLexicalAnalyzer::T_OPERATOR) FilterLexicalAnalyzer::T_ID etc,...
        
        if ($this->getLookAhead() == FilterLexicalAnalyzer::T_RELOP || $this->getLookAhead() == FilterLexicalAnalyzer::T_RELOP_NE || 
            $this->getLookAhead() == FilterLexicalAnalyzer::T_RELOP_GE || $this->getLookAhead() == FilterLexicalAnalyzer::T_RELOP_LE ||
            $this->getLookAhead() == FilterLexicalAnalyzer::T_OPERATOR) {
        
            $this->match($this->getLookAhead());
            
            if ($this->getLookAhead() == FilterLexicalAnalyzer::T_ID || $this->getLookAhead() == FilterLexicalAnalyzer::T_NUMBER ||
                $this->getLookAhead() == FilterLexicalAnalyzer::T_VALUE) {
                
                $this->match($this->getLookAhead());

            } else if ($this->getLookAhead() == '(') {
                
                $this->condition();
            } else {
                // _conditionParts matchs a ID, NUMBER, VALUE or ( but not matchs a RELOP
                // after that. we can't match a ID, NUMBER, VALUE or (
                // Push error to error stack
                $input        = $this->lexer->getInput();
                $offset       = $this->lexer->getInputOffset();
                $tToken       = $this->tokenToString($this->getLookAhead(), $this->lexer->getTokenVal());
                $splitedInput = Array ('first_part'   => substr($input, 0, $offset-strlen($tToken)), 
                                       'unknown_part' => substr($input, $offset-strlen($tToken), strlen($tToken)),
                                       'last_part'    => substr($input, $offset, strlen($input)));
            
                $params = Array ('token' => $tToken, 'match' => "ID, NUMBER, VALUE or (", 'offset' => $offset,
                                 'splitedinput' => $splitedInput);

                $this->stack->push(SyntaxAnalyzer::SYNTAX_ERROR, 'error', $params, 'Syntax Error');
            }

            $this->conditionPart();
        }            
    }
    // }}}
    // {{{ setErrorMessages
    public function setErrorMessages()
    {
        $messages = Array(
            SyntaxAnalyzer::SYNTAX_ERROR       => "Syntax error near `%token%' token. I can't match `%match%' in offset: %offset%",
            SyntaxAnalyzer::UNDEFINED_ID_ERROR => "Undefined ID '%id%'"
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
