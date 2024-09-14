<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer;

use Exception;

interface ProtoLexerInterface
{
    /**
     * Lexical analysis of the input string
     *
     * @return Token|null
     */
    public function moveNext(): ?Token;

    /**
     * Set the input string to be analyzed
     *
     * @param string $input
     */
    public function setInput(string $input): self;

    /**
     * Get the current token
     *
     * @return Token|null
     */
    public function getCurrentToken(): ?Token;
}
