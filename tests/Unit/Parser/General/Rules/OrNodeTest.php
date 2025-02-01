<?php

declare(strict_types=1);

namespace ProtoReflection\Tests\Unit\Parser\General\Rules;

use PHPUnit\Framework\TestCase;
use ProtoReflection\Lexer\Protobuf\Token;
use ProtoReflection\Lexer\Protobuf\TokenType;
use ProtoReflection\Parser\General\AST;
use ProtoReflection\Parser\General\Rules\MatchValue;
use ProtoReflection\Parser\General\Rules\OrNode;

final class OrNodeTest extends TestCase
{
    public function testGenerateASTValid(): void
    {
        $orNode = new OrNode([
            new MatchValue('hello'),
            new MatchValue('world')
        ]);

        $result = $orNode->generateAST([new Token(TokenType::T_STRING, 'hello'), new Token(TokenType::T_STRING, 'test')]);

        self::assertTrue($result->isValid());
        self::assertInstanceOf(AST::class, $result->getAST());
        self::assertEquals([new Token(TokenType::T_STRING, 'test')], $result->getRemainingTokens());
    }

    public function testGenerateASTSecondRuleMatches(): void
    {
        $orNode = new OrNode([
            new MatchValue('hello'),
            new MatchValue('world')
        ]);

        $result = $orNode->generateAST([new Token(TokenType::T_STRING, 'world'), new Token(TokenType::T_STRING, 'test')]);

        self::assertTrue($result->isValid());
        self::assertInstanceOf(AST::class, $result->getAST());
        self::assertEquals([new Token(TokenType::T_STRING, 'test')], $result->getRemainingTokens());
    }

    public function testGenerateASTInvalid(): void
    {
        $orNode = new OrNode([
            new MatchValue('hello'),
            new MatchValue('world')
        ]);

        $result = $orNode->generateAST([new Token(TokenType::T_STRING, 'test')]);

        self::assertFalse($result->isValid());
        self::assertEquals('No rules matched', $result->getError());
    }

    public function testAddRule(): void
    {
        $orNode = new OrNode([
            'test' => new MatchValue('test')
        ]);

        $result = $orNode->generateAST([new Token(TokenType::T_STRING, 'test')]);

        self::assertTrue($result->isValid());
        self::assertInstanceOf(AST::class, $result->getAST());
        self::assertEmpty($result->getRemainingTokens());
    }
}
