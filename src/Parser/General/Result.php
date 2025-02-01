<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General;

use ProtoReflection\Lexer\TokenInterface;

final class Result
{
    /**
     * @param array<array-key, TokenInterface> $tokensLeft
     */
    private function __construct(
        private readonly bool $valid,
        private readonly ASTBuilder $builder,
        private readonly array $tokensLeft = [],
        private readonly ?string $error = null,
        private readonly ?AST $ast = null
    ) {
    }

    /**
     * @param array<array-key, TokenInterface> $tokensLeft
     */
    public static function valid(ASTBuilder $builder, AST $ast, array $tokensLeft): self
    {
        return new self(true, $builder, $tokensLeft, null, $ast);
    }

    /**
     * @param array<array-key, TokenInterface> $tokensLeft
     */
    public static function invalid(ASTBuilder $builder, string $error, array $tokensLeft = []): self 
    {
        return new self(false, $builder, $tokensLeft, $error);
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getAST(): ?AST
    {
        return $this->ast;
    }

    /**
     * @return array<array-key, TokenInterface>
     */
    public function getRemainingTokens(): array
    {
        return $this->tokensLeft;
    }
}
