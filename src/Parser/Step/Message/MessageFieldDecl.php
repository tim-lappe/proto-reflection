<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\Step\Message;

use Exception;
use ProtoReflection\Lexer\TokenType;
use ProtoReflection\Parser\AST\MessageFieldDeclNode;
use ProtoReflection\Parser\Step\AbstractStep;

class MessageFieldDecl extends AbstractStep
{
    /**
     * @throws Exception
     */
    public function parseMessageField(): MessageFieldDeclNode
    {
        if (!$this->match()) {
            throw new Exception('Syntax error. Expected T_IDENTIFIER instead of ' . $this->currentToken->type->value);
        }

        $fieldCardinality = null;
        if (in_array($this->currentToken->type, TokenType::getFieldCardinalityTokens(), true)) {
            $fieldCardinality = $this->currentToken->value;
            $this->move();
        }

        if ($this->currentToken !== TokenType::T_IDENTIFIER) {
            throw new Exception('Syntax error. Expected T_IDENTIFIER instead of ' . $this->currentToken->type->value);
        }

        $fieldName = $this->currentToken->value;
        $this->move();

        if ($this->currentToken !== TokenType::T_EQUALS) {
            throw new Exception('Syntax error. Expected T_EQUALS instead of ' . $this->currentToken->type->value);
        }

        $this->move();
        if ($this->currentToken->type !== TokenType::T_INT_LITERAL) {
            throw new Exception('Syntax error. Expected T_INT_LITERAL instead of ' . $this->currentToken->type->value);
        }

        $fieldNumber = (int) $this->currentToken->value;
        $this->move();

        if ($this->currentToken !== TokenType::T_SEMICOLON) {
            throw new Exception('Syntax error. Expected T_SEMICOLON instead of ' . $this->currentToken->type->value);
        }

        return new MessageFieldDeclNode($fieldCardinality, $this->currentToken->value, $fieldName, $fieldNumber);
    }

    public function match(): bool
    {
        return in_array(
            $this->currentToken->type,
            [
                ...TokenType::getFieldCardinalityTokens(),
                TokenType::T_IDENTIFIER,
            ],
            true
        );
    }
}
