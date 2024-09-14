<?php

declare(strict_types=1);

namespace ProtoReflection\Test\Unit\Parser\Step;

use PHPUnit\Framework\TestCase;
use ProtoReflection\Lexer\ProtoLexer;
use ProtoReflection\Lexer\ProtoLexerInterface;
use Throwable;

abstract class ParserStepTestCase extends TestCase
{
    protected function createLexer(string $input): ProtoLexerInterface
    {
        $lexer = new ProtoLexer();
        $lexer->setInput($input);

        try {
            $lexer->moveNext();
        } catch (Throwable $e) {
            self::fail('Unexpected Exception');
        }

        return $lexer;
    }
}
