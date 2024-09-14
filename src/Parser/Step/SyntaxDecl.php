<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\Step;

use Exception;
use ProtoReflection\Lexer\TokenType;
use ProtoReflection\Parser\AST\SyntaxNode;
use ProtoReflection\Parser\StringUtils;

class SyntaxDecl extends AbstractStep
{
    /**
     * @return SyntaxNode
     * @throws Exception
     */
    public function parseSyntax(): SyntaxNode
    {
        if (!$this->match()) {
            throw new Exception('Syntax error. Expected T_SYNTAX instead of ' . (string) $this->currentToken?->value);
        }

        $this->move();
        if ($this->currentToken === null || $this->currentToken->type !== TokenType::T_EQUALS) {
            throw new Exception('Syntax error. Expected T_EQUALS instead of ' . (string) $this->currentToken?->value);
        }

        $this->move();
        if ($this->currentToken === null || $this->currentToken->type !== TokenType::T_STRING_LITERAL) {
            throw new Exception('Syntax error. Expected T_STRING_LITERAL instead of ' . (string) $this->currentToken?->value);
        }

        $node = new SyntaxNode(StringUtils::stripQuotes($this->currentToken->value));

        $this->move();
        if ($this->currentToken === null || $this->currentToken->type !== TokenType::T_SEMICOLON) {
            throw new Exception('Syntax error. Expected T_SEMICOLON instead of ' . (string) $this->currentToken?->value);
        }

        return $node;
    }

    public function match(): bool
    {
        return $this->currentToken?->type === TokenType::T_SYNTAX;
    }
}
