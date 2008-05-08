<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class ColumnSyntaxAnalyzer extends SyntaxAnalyzer
{
    // {{{ properties
    
    var $lookAhead   = Null;
    var $symbolTable = Null;

    // }}}
    // {{{ Constructor
    public function ColumnSyntaxAnalyzer()
    {
        // Set symbol table
        $this->symbolTable = $lexer->getSymbolTable();

        parent::__construct(new ColumnLexicalAnalyzer);

        // Set Error messages
        $this->setErrorMessages();
    }
    // }}}
    // {{{ analyze
    public function analyze($input)
    {
        parent::analyze(trim(preg_replace('/\s+/', ' ', $input)));

        // Call the grammer
        $this->stmt();
    }
    // }}}
    // {{{ match
    protected function match($token)
    {
        // DEBUGGING:
        // echo $this->tokenToString($token , $token) . ' ';

        if ($token == ColumnLexicalAnalyzer::T_ID) {
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
    // {{{ tokenToString
    protected function tokenToString($token , $tokenVal)
    {
        switch ($token) {
            case ColumnLexicalAnalyzer::T_ID:       $stredToken = (trim($tokenVal))?$tokenVal:'ID'; break;
            case ColumnLexicalAnalyzer::T_NUMBER:   $stredToken = (trim($tokenVal))?$tokenVal:'NUMBER'; break;
            case ColumnLexicalAnalyzer::T_FUNCTION: $stredToken = (trim($tokenVal))?$tokenVal:'FUNCTION'; break;
            case ColumnLexicalAnalyzer::T_EOI:      $stredToken = 'EOI'; break;
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
    protected function stmt() { $this->mathStmt(); $this->match(ColumnLexicalAnalyzer::T_EOI); }
    protected function mathStmt()
    {
        if ($this->getLookAhead() == ColumnLexicalAnalyzer::T_ID || $this->getLookAhead() == ColumnLexicalAnalyzer::T_NUMBER) {
        
            $this->match($this->getLookAhead());
        
        } else if ($this->getLookAhead() == '(') {
            
            $this->match('('); $this->mathStmt(); $this->match(')');
        
        } else if ($this->getLookAhead() == ColumnLexicalAnalyzer::T_FUNCTION) {
            
            $this->match(ColumnLexicalAnalyzer::T_FUNCTION); $this->match('('); $this->match(ColumnLexicalAnalyzer::T_ID); $this->match(')'); 
        
        } else {

            // Push error to error stack
            $input        = $this->lexer->getInput();
            $offset       = $this->lexer->getInputOffset();
            $tToken       = $this->tokenToString($this->lookAhead, $this->lexer->getTokenVal());
            $splitedInput = Array ('first_part'   => substr($input, 0, $offset-strlen($tToken)), 
                                   'unknown_part' => substr($input, $offset-strlen($tToken), strlen($tToken)),
                                   'last_part'    => substr($input, $offset, strlen($input)));
            
            $params = Array ('token' => $tToken, 'match' => "(', `ID' or `NUMBER", 'offset' => $offset,
                             'splitedinput' => $splitedInput);

            $this->stack->push(SyntaxAnalyzer::SYNTAX_ERROR, 'error', $params, 'Syntax Error');
        }

        // After every _mathStmt we should have _mathStmtSecPart
        $this->mathStmtSecPart();
    }
    protected function mathStmtSecPart()
    {
        if ($this->getLookAhead() == '+' || $this->getLookAhead() == '-' || 
            $this->getLookAhead() == '*' || $this->getLookAhead() == '/' || 
            $this->getLookAhead() == '%') {
            
            $this->match($this->getLookAhead()); $this->mathStmt();
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
