<?php

declare(strict_types=1);

namespace ProtoReflection\Test\Unit\Parser\Step;

use Exception;
use ProtoReflection\Parser\Step\SyntaxDecl;

class SyntaxDeclTest extends ParserStepTestCase
{
    /**
     * @throws Exception
     *
     * @dataProvider correctSyntaxDeclProvider
     */
    public function testSyntaxDeclIsCorrect(string $input): void
    {
        self::expectNotToPerformAssertions();

        $syntaxDecl = new SyntaxDecl($this->createLexer($input));
        $syntaxDecl->parseSyntax();
    }

    public static function correctSyntaxDeclProvider(): iterable
    {
        yield 'proto3' => ['syntax = "proto3";'];
        yield 'proto2' => ['syntax = "proto2";'];
    }

    /**
     * @dataProvider incorrectSyntaxDeclProvider
     * @param string $input
     * @throws Exception
     */
    public function testSyntaxDeclIsIncorrect(string $input): void
    {
        self::expectException(Exception::class);

        $syntaxDecl = new SyntaxDecl($this->createLexer($input));
        $syntaxDecl->parseSyntax();
    }

    /**
     * @return iterable
     */
    public static function incorrectSyntaxDeclProvider(): iterable
    {
        yield 'Missing semicolon' => ['syntax = "proto3"'];
        yield 'Missing equals' => ['syntax "proto3";'];
        yield 'Missing syntax' => ['= "proto3";'];
        yield 'Missing value' => ['syntax = ;'];
        yield 'Missing quotes' => ['syntax = proto3;'];
    }
}
