<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General\Rules;

use ProtoReflection\Parser\General\AST;
use ProtoReflection\Parser\General\ASTBuilder;
use ProtoReflection\Parser\General\Result;

final class Optional implements ASTBuilder
{
    public function __construct(
        private readonly ASTBuilder $builder
    ) {
    }

    public function generateAST(array $tokens): Result
    {
        $result = $this->builder->generateAST($tokens);
        if ($result->isValid()) {
            return $result;
        }

        return Result::valid($this, new AST(), $tokens);
    }
}
