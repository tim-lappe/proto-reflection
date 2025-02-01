<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General\Rules;

use ProtoReflection\Parser\General\AST;
use ProtoReflection\Parser\General\ASTBuilder;
use ProtoReflection\Parser\General\Node;
use ProtoReflection\Parser\General\Result;

/**
 * Represents a logical OR rule that matches if any of its child rules match
 */
final class OrNode implements ASTBuilder
{
    /**
     * @param array<array-key, ASTBuilder> $rules
     */
    public function __construct(private array $rules = [], private bool $isOptional = false, private ?string $key = null)
    {
        
    }

    /**
     * @inheritDoc
     */
    public function generateAST(array $tokens): Result
    {
        foreach ($this->rules as $key => $rule) {
            $result = $rule->generateAST($tokens);
            $ast = $result->getAST();
            if ($result->isValid() && $ast !== null) {
                if (!is_string($key)) {
                    return Result::valid($this, $ast, $result->getRemainingTokens());
                }
                $ast->setKey($key);
                return Result::valid($this, $ast, $result->getRemainingTokens());
            }
        }

        if ($this->isOptional) {
            return Result::valid($this, new AST(), $tokens);
        }

        return Result::invalid($this, 'No rules matched');
    }
}