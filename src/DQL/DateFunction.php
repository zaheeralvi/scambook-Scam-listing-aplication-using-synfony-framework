<?php

namespace App\DQL;

use Doctrine\ORM\Query\Lexer;

class DateFunction extends \Doctrine\ORM\Query\AST\Functions\FunctionNode
{
    public $numberExpression = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        //Check for correct
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->numberExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'DATE(' .
        $this->numberExpression->dispatch($sqlWalker). ')';
    }
}