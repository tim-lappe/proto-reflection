<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\Step;

use Exception;
use ProtoReflection\Lexer\TokenType;
use ProtoReflection\Parser\AST\OptionDeclNode;

class CompactOptionDecl extends AbstractStep
{
    /**
     * @throws Exception
     */
    public function parseCompactOptions(): void
    {
        if (!$this->match()) {
            throw new Exception('Syntax error. Expected T_LEFT_BRACKET instead of ' . (string) $this->currentToken?->type->value);
        }

        $this->move();
    }

    /**
     * @throws Exception
     */
    private function parseSingleCompactOption(): OptionDeclNode
    {
        $optionNameParser = new OptionNameDecl($this->lexer);
        $optionName = $optionNameParser->parseOptionName();

        if ($this->currentToken === null || $this->currentToken->type !== TokenType::T_EQUALS) {
            throw new Exception('Syntax error. Expected T_EQUALS instead of ' . (string) $this->currentToken?->type->value);
        }

        $this->move();
        if ($this->currentToken === null || in_array($this->currentToken->type, TokenType::getScalarValueTypes(), true)) {
            throw new Exception('Syntax error. Expected ScalarType instead of ' . $this->currentToken->type->value);
        }

        return new OptionDeclNode($optionName, $this->currentToken->value);
    }

    public function match(): bool
    {
        return $this->currentToken?->type === TokenType::T_LEFT_BRACKET;
    }
}
