<?php

declare(strict_types=1);

namespace ProtoReflection\Tests\Unit\Parser\General\Rules;

use PHPUnit\Framework\TestCase;
use ProtoReflection\Lexer\Protobuf\Token;
use ProtoReflection\Lexer\Protobuf\TokenType;
use ProtoReflection\Parser\General\AST;
use ProtoReflection\Parser\General\Rules\MatchType;

final class MatchTypeTest extends TestCase
{
    public function testGenerateASTValid(): void
    {
        $matcher = new MatchType(TokenType::T_STRING->value);
        $result = $matcher->generateAST([new Token(TokenType::T_STRING, 'test'), new Token(TokenType::T_STRING, 'other')]);

        self::assertTrue($result->isValid());
        self::assertInstanceOf(AST::class, $result->getAST());
        self::assertEquals('test', $result->getAST()->getValue());
        self::assertEquals([new Token(TokenType::T_STRING, 'other')], $result->getRemainingTokens());
    }

    public function testGenerateASTInvalidEmpty(): void
    {
        $matcher = new MatchType('test');
        $result = $matcher->generateAST([]);

        self::assertFalse($result->isValid());
        self::assertEquals('No tokens to match', $result->getError());
    }

    public function testGenerateASTInvalidToken(): void
    {
        $matcher = new MatchType('test');
        $result = $matcher->generateAST([new Token(TokenType::T_STRING, 'wrong')]);

        self::assertFalse($result->isValid());
        self::assertEquals('Expected token "test", got "STRING"', $result->getError());
    }
}
