<?php

declare(strict_types=1);

namespace ProtoReflection\Tests\Unit\Parser\General;

use PHPUnit\Framework\TestCase;
use ProtoReflection\Parser\General\Rules\Chain;
use ProtoReflection\Parser\General\Rules\OrNode;
use ProtoReflection\Parser\General\Rules\RegexMatch;
use ProtoReflection\Parser\General\Rules\Repeat;
use ProtoReflection\Parser\General\Rules\MatchValue;
use ProtoReflection\Lexer\Protobuf\Token;
use ProtoReflection\Lexer\Protobuf\TokenType;

final class ASTBuilderTest extends TestCase
{
    public function testGenerateAST(): void
    {
        $stringLiteral = new RegexMatch('/^"[^"]*"$/');

        $identifier = new Chain([
            'LETTER' => new RegexMatch('/^[a-zA-Z_][a-zA-Z0-9_]*$/'),
            'DOT' => new MatchValue('.'),
        ]);

        $fullIdentifier = new Chain([
            'IDENTIFIER' => new RegexMatch('/^[a-zA-Z_][a-zA-Z0-9_]*$/'),
            'DOT' => new MatchValue('.'),
        ]);

        $packageStatement = new Chain([
            'PACKAGE' => new MatchValue('package'),
            'STRING_LITERAL' => $stringLiteral,
            'SEMI' => new MatchValue(';'),
        ]);

        $importStatement = new Chain([
            'IMPORT' => new MatchValue('import'),
            'WEAK_PUBLIC' => new Repeat(new OrNode([
                new MatchValue('weak'),
                new MatchValue('public'),
            ]), 0, 1),
            'STRING_LITERAL' => $stringLiteral,
            'SEMI' => new MatchValue(';'),
        ]);

        $root = new Chain([
            'syntax' => new Chain([
                'SYNTAX' => new MatchValue("syntax"),
                'EQ' => new MatchValue('='),
                'PROTO_LIT' => new OrNode([
                    new MatchValue('"proto3"'),
                    new MatchValue('\'proto3\''),
                ]),
                'SEMI' => new MatchValue(';'),
            ]),
            '*' => new Repeat($importStatement),
            'EOF' => new MatchValue('EOF'),
        ]);

        $tokens = [
            new Token(TokenType::T_SYNTAX, 'syntax'),
            new Token(TokenType::T_EQUALS, '='),
            new Token(TokenType::T_STRING, '"proto3"'),
            new Token(TokenType::T_SEMICOLON, ';'),
            new Token(TokenType::T_IMPORT, 'import'),
            new Token(TokenType::T_WEAK, 'weak'),
            new Token(TokenType::T_STRING, '"foo"'),
            new Token(TokenType::T_SEMICOLON, ';'),
            new Token(TokenType::T_IDENTIFIER, 'EOF'),
        ];
        $result = $root->generateAST($tokens);

        self::assertTrue($result->isValid());
        self::assertNotNull($result->getAST());
    }
}
