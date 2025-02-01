<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer\Protobuf;

use ProtoReflection\Lexer\TokenInterface;

class Token implements TokenInterface
{
    public function __construct(
        public TokenType $type,
        public string $value
    ) {
    }
    /**
     * @inheritDoc
     */
    public function getType(): string 
    {
        return $this->type->value;
    }
    
    /**
     * @inheritDoc
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
