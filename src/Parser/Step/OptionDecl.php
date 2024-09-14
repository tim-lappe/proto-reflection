<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\Step;

use Exception;
use ProtoReflection\Lexer\TokenType;
use ProtoReflection\Parser\AST\OptionDeclNode;

class OptionDecl extends AbstractStep
{
    /**
     * @throws Exception
     */
    public function parseOption(): OptionDeclNode
    {
        if (!$this->match() || $this->currentToken === null) {
            throw new Exception('Syntax error. Expected T_OPTION instead of ' . (string) $this->currentToken?->value);
        }

        $this->move();

        $optionNameParser = new OptionNameDecl($this->lexer);
        $optionName = $optionNameParser->parseOptionName();

        if ($this->currentToken === null || $this->currentToken->type !== TokenType::T_EQUALS) {
            throw new Exception('Syntax error. Expected T_EQUALS instead of ' . (string) $this->currentToken?->value);
        }

        return new OptionDeclNode($optionName, $this->currentToken->value);
    }

    public function match(): bool
    {
        return $this->currentToken?->type === TokenType::T_OPTION;
    }
}
