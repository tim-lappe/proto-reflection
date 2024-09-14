<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\Step;

use Exception;
use ProtoReflection\Lexer\TokenType;

class OptionValueDecl extends AbstractStep
{
    /**
     * @throws Exception
     */
    public function parseOptionValue(): string
    {
        if (!$this->match() || $this->currentToken === null) {
            throw new Exception('Syntax error. Expected ScalarType or "{" instead of ' . (string) $this->currentToken?->type->value);
        }

        if ($this->currentToken->type === TokenType::T_LEFT_BRACE) {
            $value = $this->currentToken->value;
        } else {
            $value = $this->currentToken->type->value;
        }
    }

    public function parseMessageTextFormat()
    {
    }

    public function match(): bool
    {
        if ($this->currentToken === null) {
            return false;
        }

        return $this->currentToken->type->isScalarType() || $this->currentToken === TokenType::T_LEFT_BRACE;
    }
}
