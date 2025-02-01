<?php

declare(strict_types=1);

namespace ProtoReflection\Test\Unit\Lexer\G4;

use PHPUnit\Framework\TestCase;
use ProtoReflection\Lexer\G4\DoctrineG4Lexer;
use ProtoReflection\Lexer\G4\TokenType;

class DoctrineG4LexerTest extends TestCase
{
    private DoctrineG4Lexer $lexer;

    protected function setUp(): void
    {
        $this->lexer = new DoctrineG4Lexer();
    }

    public function testLexerRecognizesKeywords(): void
    {
        $this->lexer->setInput('grammar parser lexer');
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_GRAMMAR, $this->lexer->lookahead->type);
        
        $this->lexer->moveNext(); 
        self::assertEquals(TokenType::T_PARSER, $this->lexer->lookahead->type);
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_LEXER, $this->lexer->lookahead->type);
    }

    public function testLexerRecognizesOperators(): void 
    {
        $this->lexer->setInput('= | * + ?');
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_EQUALS, $this->lexer->lookahead->type);
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PIPE, $this->lexer->lookahead->type);
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_STAR, $this->lexer->lookahead->type);
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PLUS, $this->lexer->lookahead->type);
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_QUESTION, $this->lexer->lookahead->type);
    }

    public function testLexerRecognizesIdentifiers(): void
    {
        $this->lexer->setInput('myRule test123 UPPER_CASE');
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('myRule', $this->lexer->lookahead->value);
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('test123', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('UPPER_CASE', $this->lexer->lookahead->value);
    }

    public function testLexerRecognizesComments(): void
    {
        $this->lexer->setInput("// Single line comment");
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_COMMENT, $this->lexer->lookahead->type, 'Single line comment');
        
        $this->lexer->setInput("/* Multi line comment */");

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_COMMENT, $this->lexer->lookahead->type, 'Multi line comment');

        $this->lexer->setInput("/* Multi line\n comment */");
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_COMMENT, $this->lexer->lookahead->type, 'Multi line comment');

    }

    public function testLexerIgnoresWhitespace(): void
    {
        $this->lexer->setInput("grammar    \n\t  parser");
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_GRAMMAR, $this->lexer->lookahead->type);
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PARSER, $this->lexer->lookahead->type);
    }

    public function testLexerRecognizesComplexTokens(): void
    {
        $this->lexer->setInput("rule1 -> rule2 | rule3*");
        
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('rule1', $this->lexer->lookahead->value);

        $this->lexer->moveNext(); 
        self::assertEquals(TokenType::T_ARROW, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('rule2', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PIPE, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('rule3', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_STAR, $this->lexer->lookahead->type);
    }

    public function testLexerRecognizesExampleGrammar(): void
    {
        $this->lexer->setInput(<<<GRAMMAR
grammar Protobuf3;

proto
    : syntax (importStatement | packageStatement | optionStatement | topLevelDef | emptyStatement_)* EOF
    ;

// Syntax

syntax
    : SYNTAX EQ (PROTO3_LIT_SINGLE | PROTO3_LIT_DOBULE) SEMI
    ;

// Import Statement

importStatement
    : IMPORT (WEAK | PUBLIC)? strLit SEMI
    ;

GRAMMAR);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_GRAMMAR, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('Protobuf3', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_SEMICOLON, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('proto', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_COLON, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('syntax', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_LEFT_PAREN, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('importStatement', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PIPE, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('packageStatement', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PIPE, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('optionStatement', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PIPE, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('topLevelDef', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_PIPE, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('emptyStatement_', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_RIGHT_PAREN, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_STAR, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_EOF, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_SEMICOLON, $this->lexer->lookahead->type);
        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_COMMENT, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('syntax', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_COLON, $this->lexer->lookahead->type);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('SYNTAX', $this->lexer->lookahead->value);

        $this->lexer->moveNext();
        self::assertEquals(TokenType::T_IDENTIFIER, $this->lexer->lookahead->type);
        self::assertEquals('EQ', $this->lexer->lookahead->value);
    }
}
