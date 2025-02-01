<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer;

interface TokenInterface
{
    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @return string
     */
    public function getType(): string;
}
