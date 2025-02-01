<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General\Rules;

use ProtoReflection\Parser\General\ASTBuilder;
use ProtoReflection\Parser\General\Result;
use ProtoReflection\Parser\General\AST;

final class Chain implements ASTBuilder
{
    /**
     * @param array<array-key, ASTBuilder> $builders
     */
    public function __construct(
        private readonly array $builders = [],
        private readonly ?string $key = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function generateAST(array $tokens): Result
    {
        $ast = new AST();
        $lastTokens = $tokens;
        $ast->setKey($this->key);
        foreach ($this->builders as $key => $builder) {
            $result = $builder->generateAST($lastTokens);
            
            if (!$result->isValid()) {
                return $result;
            }

            $remainingTokens = $result->getRemainingTokens();
            if (count($remainingTokens) === count($lastTokens)) {
                continue;
            }
            
            $resultAst = $result->getAST();
            if ($resultAst === null) {
                continue;
            }

            if (is_string($key)) {
                $resultAst = AST::encapsulate($key, $resultAst);
            }

            if ($resultAst->getKey() === null) {
                $ast = $ast->merge($resultAst);
            } else {
                $ast = $ast->addChild($resultAst);
            }

            $lastTokens = $remainingTokens;
        }

        return Result::valid($this, $ast, $lastTokens);
    }
}
