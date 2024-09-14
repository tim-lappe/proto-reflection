<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer;

class Token
{
    public function __construct(
        public TokenType $type,
        public string $value
    ) {
    }
}
