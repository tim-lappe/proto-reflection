<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\Step;

use ProtoReflection\Lexer\ProtoLexerInterface;
use ProtoReflection\Lexer\Token;

abstract class AbstractStep
{
    protected ?Token $currentToken = null;

    public function __construct(
        protected ProtoLexerInterface $lexer
    ) {
        $this->currentToken = $this->lexer->getCurrentToken();
    }

    protected function move(): bool
    {
        $token = $this->lexer->moveNext();
        if ($token === null) {
            return false;
        }

        $this->currentToken = $token;

        //echo "Token: " . $this->currentToken->type->value . " Value: " . $this->currentToken->value . PHP_EOL;

        return true;
    }

    abstract public function match(): bool;
}
