<?php

namespace App\DQL;

use Doctrine\ORM\Query\Lexer;

class MultiplyFunction extends \Doctrine\ORM\Query\AST\Functions\FunctionNode
{
    public $numberExpression = null;
    public $powerExpression = 1;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        //Check for correct
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->numberExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->powerExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return
        $this->numberExpression->dispatch($sqlWalker) . ' * ' .
        $this->powerExpression->dispatch($sqlWalker);
    }
}