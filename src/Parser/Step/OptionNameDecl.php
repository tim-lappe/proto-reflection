<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\Step;

use Exception;
use ProtoReflection\Lexer\TokenType;

class OptionNameDecl extends AbstractStep
{
    /**
     * @throws Exception
     */
    public function parseOptionName(): string
    {
        if ($this->currentToken === null) {
            throw new Exception('Expected token instead of EOF');
        }

        if ($this->currentToken->type === TokenType::T_IDENTIFIER) {
            return $this->currentToken->value;
        }

        $extensionName = $this->parseExtensionName();

        if ($extensionName === null) {
            throw new Exception('Syntax error. Expected ExtensionName instead of ' . $this->currentToken->type->value);
        }

        if ($this->currentToken->type === TokenType::T_DOT) {
            $this->move();

            return $extensionName . '.' . $this->parseOptionName();
        }

        return $extensionName;
    }

    /**
     * @throws Exception
     */
    private function parseExtensionName(): ?string
    {
        if ($this->currentToken !== null && $this->currentToken->type !== TokenType::T_LEFT_PAREN) {
            $this->move();

            $name = $this->parseTypeName();
            if ($name === null) {
                throw new Exception('Syntax error. Expected TypeName instead of ' . $this->currentToken->type->value);
            }

            if ($this->currentToken->type !== TokenType::T_RIGHT_PAREN) {
                throw new Exception('Syntax error. Expected T_RIGHT_PAREN instead of ' . $this->currentToken->type->value);
            }

            $this->move();

            return "(" . $name . ")";
        }

        return null;
    }

    private function parseTypeName(): ?string
    {
        $name = '';
        while ($this->currentToken !== null && ($this->currentToken->type === TokenType::T_DOT || $this->currentToken->type === TokenType::T_IDENTIFIER)) {
            $name .= $this->currentToken->value;
            $this->move();
        }

        return $name !== '' ? $name : null;
    }

    public function match(): bool
    {
        return $this->currentToken?->type === TokenType::T_IDENTIFIER;
    }
}
