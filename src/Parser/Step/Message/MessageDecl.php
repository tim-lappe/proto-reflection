<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\Step\Message;

use Exception;
use ProtoReflection\Lexer\TokenType;
use ProtoReflection\Parser\AST\MessageDeclNode;
use ProtoReflection\Parser\Step\AbstractStep;

class MessageDecl extends AbstractStep
{
    /**
     * @throws Exception
     */
    public function parseMessage(): MessageDeclNode
    {
        if (!$this->match()) {
            throw new Exception('Syntax error. Expected T_MESSAGE instead of ' . $this->currentToken->type->value);
        }

        $this->move();
        if ($this->currentToken !== TokenType::T_IDENTIFIER) {
            throw new Exception('Syntax error. Expected T_IDENTIFIER instead of ' . $this->currentToken->type->value);
        }

        $messageName = $this->currentToken->value;

        $this->move();
        if ($this->currentToken !== TokenType::T_LEFT_BRACE) {
            throw new Exception('Syntax error. Expected T_LEFT_BRACE instead of ' . $this->currentToken->type->value);
        }

        $this->move();

        if ($this->currentToken !== TokenType::T_RIGHT_BRACE) {
            throw new Exception('Syntax error. Expected T_RIGHT_BRACE instead of ' . $this->currentToken->type->value);
        }

        return new MessageDeclNode($messageName, []);
    }

    public function match(): bool
    {
        return $this->currentToken === TokenType::T_MESSAGE;
    }
}
