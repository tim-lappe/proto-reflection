<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General\Rules;

use ProtoReflection\Parser\General\ASTBuilder;
use ProtoReflection\Parser\General\Result;
use ProtoReflection\Parser\General\Token;

/**
 * A rule that lazily evaluates its inner rule
 */
final class LazyRule implements ASTBuilder
{
    /**
     * @param \Closure(): ASTBuilder $ruleBuilderClosure
     */
    public function __construct(
        private \Closure $ruleBuilderClosure
    ) {
    }
    
    public function generateAST(array $tokens): Result
    {
        return ($this->ruleBuilderClosure)()->generateAST($tokens);
    }
}
