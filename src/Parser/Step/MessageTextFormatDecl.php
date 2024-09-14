<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\Step;

use Exception;
use ProtoReflection\Lexer\TokenType;

class MessageTextFormatDecl extends AbstractStep
{
    public function parseMessageTextFormat()
    {
    }

    public function parseMessageLiteralField()
    {
    }

    /**
     * @throws Exception
     */
    public function parseMessageLiteralFieldName(): string
    {
        if ($this->currentToken === TokenType::T_IDENTIFIER) {
            return $this->currentToken->value;
        }

        if ($this->currentToken->type !== TokenType::T_LEFT_BRACKET) {
            throw new Exception('Syntax error. Expected T_LEFT_BRACKET or Identifier instead of ' . $this->currentToken->type->value);
        }

        $fieldName = $this->parseSpecialFieldName();

        $this->move();
        if ($this->currentToken !== TokenType::T_RIGHT_BRACKET) {
            throw new Exception('Syntax error. Expected T_RIGHT_BRACKET instead of ' . $this->currentToken->type->value);
        }

        return $fieldName;
    }

    /**
     * @throws Exception
     */
    public function parseSpecialFieldName(): string
    {
        if ($this->currentToken === TokenType::T_IDENTIFIER) {
            return $this->currentToken->value;
        }

        if ($this->currentToken->type !== TokenType::T_LEFT_BRACKET) {
            throw new Exception('Syntax error. Expected T_LEFT_BRACKET or Identifier instead of ' . $this->currentToken->type->value);
        }

        $this->move();
        if ($this->currentToken !== TokenType::T_STRING_LITERAL) {
            throw new Exception('Syntax error. Expected T_STRING_LITERAL instead of ' . $this->currentToken->type->value);
        }

        $fieldName = $this->currentToken->value;

        if ($this->currentToken !== TokenType::T_SLASH) {
            return $fieldName;
        }

        $this->move();
        if ($this->currentToken !== TokenType::T_STRING_LITERAL) {
            throw new Exception('Syntax error. Expected T_STRING_LITERAL instead of ' . $this->currentToken->type->value);
        }

        return $fieldName . '/' . $this->currentToken->value;
    }

    public function match(): bool
    {
        return $this->currentToken === TokenType::T_IDENTIFIER || $this->currentToken === TokenType::T_LEFT_BRACKET;
    }
}
