<?php

declare(strict_types=1);

namespace ProtoReflection\Tests\Unit\Lexer\Protobuf;

use PHPUnit\Framework\TestCase;
use ProtoReflection\Lexer\Protobuf\DoctrineProtoLexer;
use ProtoReflection\Lexer\Protobuf\TokenType;

final class DoctrineProtobufLexerTest extends TestCase
{
    private DoctrineProtoLexer $lexer;

    protected function setUp(): void
    {
        $this->lexer = new DoctrineProtoLexer();
    }

    public function testLexerRecognizesKeywords(): void
    {
        $this->lexer->setInput('syntax edition import weak public package');

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_SYNTAX, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_EDITION, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IMPORT, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_WEAK, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PUBLIC, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PACKAGE, $this->lexer->lookahead->type);
    }

    public function testLexerRecognizesLiterals(): void
    {
        $this->lexer->setInput('"string" 123 123.45 0xFF');

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_STRING_LITERAL, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_INT_LITERAL, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_FLOAT_LITERAL, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_INT_LITERAL, $this->lexer->lookahead->type);
    }

    public function testLexerRecognizesComments(): void
    {
        $this->lexer->setInput("// Line comment\n/* Block comment */");

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_LINE_COMMENT, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_BLOCK_COMMENT, $this->lexer->lookahead->type);
    }

    public function testLexerRecognizesMultilineBlockComment(): void
    {
        $this->lexer->setInput("/* This is a\nmultiline block\ncomment */");

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_BLOCK_COMMENT, $this->lexer->lookahead->type);
    }

    public function testLexerRecognizesProto3Keywords(): void
    {
        $this->lexer->setInput('message enum service rpc stream returns oneof map');

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_MESSAGE, $this->lexer->lookahead->type);

        $this->lexer->moveNext(); 
        self::assertEquals(TokenType::T_ENUM, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_SERVICE, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_RPC, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_STREAM, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_RETURNS, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_ONEOF, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_MAP, $this->lexer->lookahead->type);
    }

    public function testLexerRecognizesProto3Literals(): void
    {
        $this->lexer->setInput('"proto3" \'proto3\'');

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PROTO3_LIT_SINGLE, $this->lexer->lookahead->type);
        self::assertEquals('"proto3"', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PROTO3_LIT_DOUBLE, $this->lexer->lookahead->type);
        self::assertEquals('\'proto3\'', $this->lexer->lookahead->value);
    }

    public function testLexerRecognizesIdentifiers(): void
    {
        $this->lexer->setInput('myIdentifier _underscore123');

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('myIdentifier', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('_underscore123', $this->lexer->lookahead->value);
    }
}
