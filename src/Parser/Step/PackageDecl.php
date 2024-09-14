<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\Step;

use Exception;
use ProtoReflection\Lexer\TokenType;
use ProtoReflection\Parser\StringUtils;

class PackageDecl extends AbstractStep
{
    /**
     * @throws Exception
     */
    public function parsePackage(): string
    {
        if (!$this->match() || $this->currentToken === null) {
            throw new Exception('Syntax error. Expected T_PACKAGE instead of ' . (string) $this->currentToken?->type->value);
        }

        $this->move();
        if ($this->currentToken === null || $this->currentToken->type !== TokenType::T_IDENTIFIER) {
            throw new Exception('Syntax error. Expected T_STRING_LITERAL instead of ' . (string) $this->currentToken?->type->value);
        }

        $package = StringUtils::stripQuotes($this->currentToken->value);

        $this->move();
        if ($this->currentToken === null || $this->currentToken->type !== TokenType::T_SEMICOLON) {
            throw new Exception('Syntax error. Expected T_SEMICOLON instead of ' . (string) $this->currentToken?->type->value);
        }

        return $package;
    }

    public function match(): bool
    {
        return $this->currentToken?->type === TokenType::T_PACKAGE;
    }
}
