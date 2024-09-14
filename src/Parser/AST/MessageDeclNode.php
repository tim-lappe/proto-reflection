<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\AST;

class MessageDeclNode
{
    public function __construct(
        public string $messageName,
        public array $fields
    ) {
    }
}
