<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer\Protobuf;

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
            $this->doctrineProtoLexer->lookahead->value ?? throw new Exception('Unexpected null value')
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

        $type = $this->doctrineProtoLexer->lookahead->type;
        $value = $this->doctrineProtoLexer->lookahead->value;

        if ($type instanceof TokenType) {
            return new Token($type, (string) $value);
        }

        return null;
    }
}
