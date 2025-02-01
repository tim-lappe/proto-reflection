<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General\Rules;

use ProtoReflection\Parser\General\AST;
use ProtoReflection\Parser\General\ASTBuilder;
use ProtoReflection\Parser\General\Result;

final class RegexMatch implements ASTBuilder
{
    /**
     * @param non-empty-string $pattern
     */
    public function __construct(
        private readonly string $pattern,
    ) {
    }

    public function generateAST(array $tokens): Result
    {
        if (empty($tokens)) {
            return Result::invalid($this, 'No tokens to match');
        }

        if (!preg_match($this->pattern, $tokens[0]->getValue())) {
            return Result::invalid($this, sprintf('Token "%s" does not match pattern "%s"', $tokens[0]->getValue(), $this->pattern));
        }

        return Result::valid($this, (new AST())->setValue($tokens[0]->getValue()), array_slice($tokens, 1));
    }
}
