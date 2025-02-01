<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer\G4;

use Exception;

class G4Lexer 
{
    private DoctrineG4Lexer $doctrineG4Lexer;

    public function __construct()
    {
        $this->doctrineG4Lexer = new DoctrineG4Lexer();
    }

    /**
     * @throws Exception
     */
    public function moveNext(): ?Token
    {
        $hasToken = $this->doctrineG4Lexer->moveNext();
        
        if (!$hasToken) {
            return null;
        }

        return new Token(
            $this->doctrineG4Lexer->lookahead->type ?? throw new Exception('Unexpected null type'),
            $this->doctrineG4Lexer->lookahead->value ?? throw new Exception('Unexpected null value')
        );
    }

    public function setInput(string $input): self
    {
        $this->doctrineG4Lexer->setInput($input);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function getCurrentToken(): ?Token
    {
        if ($this->doctrineG4Lexer->lookahead === null) {
            return null;
        }

        $type = $this->doctrineG4Lexer->lookahead->type ?? throw new Exception('Unexpected null type');
        $value = $this->doctrineG4Lexer->lookahead->value;

        if ($type instanceof TokenType) {
            return new Token($type, (string) $value);
        }

        return null;
    }
}
