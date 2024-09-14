<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\AST;

use ProtoReflection\Model\Syntax;

readonly class SyntaxNode
{
    public function __construct(private string $syntax)
    {
    }

    public function getSyntaxValue(): string
    {
        return $this->syntax;
    }
}
