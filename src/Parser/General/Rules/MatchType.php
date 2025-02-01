<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General\Rules;

use ProtoReflection\Parser\General\AST;
use ProtoReflection\Parser\General\ASTBuilder;
use ProtoReflection\Parser\General\Result;

final class MatchType implements ASTBuilder
{
    public function __construct(
        private readonly string $type,
        private readonly ?string $key = null,
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

        if ($tokens[0]->getType() !== $this->type) {
            return Result::invalid($this, sprintf('Expected token "%s", got "%s"', $this->type, $tokens[0]->getType()));
        }

        return Result::valid($this, (new AST())
            ->setKey($this->key ?? $tokens[0]->getType())
            ->setValue($tokens[0]->getValue()), 
            array_slice($tokens, 1));
    }
}
