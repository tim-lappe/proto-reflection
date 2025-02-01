<?php

declare(strict_types=1);

namespace ProtoReflection\Tests\Unit\Parser\General\Rules;

use PHPUnit\Framework\TestCase;
use ProtoReflection\Lexer\Protobuf\Token;
use ProtoReflection\Lexer\Protobuf\TokenType;
use ProtoReflection\Parser\General\Rules\RegexMatch;

final class RegexMatchTest extends TestCase
{
    public function testGenerateASTWithEmptyTokens(): void
    {
        $rule = new RegexMatch('/test/');
        $result = $rule->generateAST([]);

        self::assertFalse($result->isValid());
        self::assertSame('No tokens to match', $result->getError());
    }

    public function testGenerateASTWithNonMatchingToken(): void 
    {
        $rule = new RegexMatch('/^[0-9]+$/');
        $result = $rule->generateAST([new Token(TokenType::T_STRING, 'abc')]);

        self::assertFalse($result->isValid());
        self::assertSame('Token "abc" does not match pattern "/^[0-9]+$/"', $result->getError());
    }

    public function testGenerateASTWithMatchingToken(): void
    {
        $rule = new RegexMatch('/^[0-9]+$/');
        $result = $rule->generateAST([new Token(TokenType::T_STRING, '123'), new Token(TokenType::T_STRING, 'abc')]);

        self::assertTrue($result->isValid());
        self::assertSame('123', $result->getAST()->getValue());
        self::assertEquals([new Token(TokenType::T_STRING, 'abc')], $result->getRemainingTokens());
    }
}
