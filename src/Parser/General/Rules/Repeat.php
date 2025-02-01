<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General\Rules;

use ProtoReflection\Parser\General\AST;
use ProtoReflection\Parser\General\ASTBuilder;
use ProtoReflection\Parser\General\Result;

final class Repeat implements ASTBuilder
{
    public function __construct(
        private readonly ASTBuilder $builder,
        private readonly int $min = 0,
        private readonly int $max = PHP_INT_MAX,
        private readonly ?string $key = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function generateAST(array $tokens): Result
    {
        $lastTokens = $tokens;
        $ast = new AST();
        $ast->setKey($this->key);

        for ($i = 0; $i < $this->min; $i++) {
            $result = $this->builder->generateAST($lastTokens);
            if (!$result->isValid()) {
                return $result;
            }

            $subAst = $result->getAST();
            if ($subAst === null) {
                return $result;
            }

            if ($ast->getKey() === null) {
                $ast = $ast->merge($subAst);
            } else {
                $ast = $ast->addChild($subAst);
            }

            $lastTokens = $result->getRemainingTokens();
        }

        for ($i = $this->min; $i < $this->max; $i++) {
            $nextResult = $this->builder->generateAST($lastTokens);
            if (!$nextResult->isValid()) {
                break;
            }

            $subAst = $nextResult->getAST();
            if ($subAst === null) {
                break;
            }

            if ($ast->getKey() === null) {
                $ast = $ast->merge($subAst);
            } else {
                $ast = $ast->addChild($subAst);
            }

            $remainingTokens = $nextResult->getRemainingTokens();
            if (count($remainingTokens) === count($lastTokens)) {
                break;
            }

            $lastTokens = $remainingTokens;
        }
        


        return Result::valid($this, $ast, $lastTokens);
    }
}

