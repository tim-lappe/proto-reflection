<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General\Rules;

use ProtoReflection\Parser\General\AST;
use ProtoReflection\Parser\General\ASTBuilder;
use ProtoReflection\Parser\General\Result;

final class MatchValue implements ASTBuilder
{
    public function __construct(
        private readonly string $value,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function generateAST(array $tokens): Result
    {
        if (empty($tokens)) {
            return Result::invalid($this, 'No tokens to match');
        }

        if ($tokens[0]->getValue() !== $this->value) {
            return Result::invalid($this, sprintf('Expected token "%s", got "%s"', $this->value, $tokens[0]->getValue()));
        }

        return Result::valid($this, (new AST())->setValue($this->value), array_slice($tokens, 1));
    }
}
