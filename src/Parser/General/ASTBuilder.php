<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General;

use ProtoReflection\Lexer\TokenInterface;
use ProtoReflection\Parser\General\AST;
use ProtoReflection\Parser\General\Grammar;

interface ASTBuilder
{
    /**
     * @param array<TokenInterface> $tokens
     * @return Result
     */
    public function generateAST(array $tokens): Result;
}

