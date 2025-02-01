<?php

declare(strict_types=1);

namespace ProtoReflection\Tests\Unit\Parser\General\Rules;

use PHPUnit\Framework\TestCase;
use ProtoReflection\Parser\General\AST;
use ProtoReflection\Parser\General\Rules\MatchValue;
use ProtoReflection\Parser\General\Rules\Repeat;
use ProtoReflection\Lexer\Protobuf\Token;
use ProtoReflection\Lexer\Protobuf\TokenType;

final class RepeatTest extends TestCase
{
    public function testGenerateASTMinimumMatches(): void
    {
        $repeat = new Repeat(new MatchValue('test'), 2);
        $result = $repeat->generateAST([new Token(TokenType::T_STRING, 'test'), new Token(TokenType::T_STRING, 'test'), new Token(TokenType::T_STRING, 'other')]);

        self::assertTrue($result->isValid());
        self::assertInstanceOf(AST::class, $result->getAST());
        self::assertEquals([new Token(TokenType::T_STRING, 'other')], $result->getRemainingTokens());
    }

    public function testGenerateASTNotEnoughMatches(): void
    {
        $repeat = new Repeat(new MatchValue('test'), 2);
        $result = $repeat->generateAST([new Token(TokenType::T_STRING, 'test'), new Token(TokenType::T_STRING, 'other')]);

        self::assertFalse($result->isValid());
    }

    public function testGenerateASTZeroMinimum(): void
    {
        $repeat = new Repeat(new MatchValue('test'), 0);
        $result = $repeat->generateAST([new Token(TokenType::T_STRING, 'other')]);

        self::assertTrue($result->isValid());
        self::assertInstanceOf(AST::class, $result->getAST());
        self::assertEquals([new Token(TokenType::T_STRING, 'other')], $result->getRemainingTokens());
    }

    public function testGenerateASTMaximumMatches(): void
    {
        $repeat = new Repeat(new MatchValue('test'), 0, 2);
        $result = $repeat->generateAST([new Token(TokenType::T_STRING, 'test'), new Token(TokenType::T_STRING, 'test'), new Token(TokenType::T_STRING, 'test'), new Token(TokenType::T_STRING, 'other')]);

        self::assertTrue($result->isValid());
        self::assertInstanceOf(AST::class, $result->getAST());
        self::assertEquals([new Token(TokenType::T_STRING, 'test'), new Token(TokenType::T_STRING, 'other')], $result->getRemainingTokens());
    }
}
