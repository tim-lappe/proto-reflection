<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer;

use Exception;

class ProtoLexer implements ProtoLexerInterface
{
    private DoctrineProtoLexer $doctrineProtoLexer;

    public function __construct()
    {
        $this->doctrineProtoLexer = new DoctrineProtoLexer();
    }

    /**
     * @throws Exception
     */
    public function moveNext(): ?Token
    {
        $hasToken = $this->doctrineProtoLexer->moveNext();
        if (!$hasToken) {
            return null;
        }

        return new Token(
            $this->doctrineProtoLexer->lookahead->type ?? throw new Exception('Unexpected null type'),
            $this->doctrineProtoLexer->lookahead->value
        );
    }

    public function setInput(string $input): self
    {
        $this->doctrineProtoLexer->setInput($input);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function getCurrentToken(): ?Token
    {
        if ($this->doctrineProtoLexer->lookahead === null) {
            return null;
        }

        return new Token(
            $this->doctrineProtoLexer->lookahead->type ?? throw new Exception('Unexpected null type'),
            $this->doctrineProtoLexer->lookahead->value
        );
    }
}
