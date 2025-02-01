<?php

declare(strict_types=1);

namespace ProtoReflection\Tests\Unit\Parser\General\Rules;

use PHPUnit\Framework\TestCase;
use ProtoReflection\Lexer\Protobuf\Token;
use ProtoReflection\Lexer\Protobuf\TokenType;
use ProtoReflection\Parser\General\AST;
use ProtoReflection\Parser\General\Rules\Chain;
use ProtoReflection\Parser\General\Rules\MatchValue;
final class ChainTest extends TestCase
{
    public function testGenerateAST(): void
    {
        $chain = new Chain([
            'first' => new MatchValue('hello'),
            'second' => new MatchValue('world')
        ]);

        $result = $chain->generateAST([new Token(TokenType::T_STRING, 'hello'), new Token(TokenType::T_STRING, 'world')]);

        self::assertTrue($result->isValid());
        self::assertInstanceOf(AST::class, $result->getAST());
        self::assertCount(2, $result->getAST()->getChildren());
        self::assertEmpty($result->getRemainingTokens());
    }

    public function testGenerateASTInvalid(): void 
    {
        $chain = new Chain([
            'first' => new MatchValue('hello'),
            'second' => new MatchValue('world')
        ]);

        $result = $chain->generateAST([new Token(TokenType::T_STRING, 'hello'), new Token(TokenType::T_STRING, 'test')]);

        self::assertFalse($result->isValid());
        self::assertNull($result->getAST());
    }
}
