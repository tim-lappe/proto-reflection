<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer\G4;

class Token
{
    public function __construct(
        public TokenType $type,
        public string $value
    ) {
    }
}
