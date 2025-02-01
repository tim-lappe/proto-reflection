<?php

declare(strict_types=1);

namespace ProtoReflection\Test\Integration\Parser;

use Exception;
use PHPUnit\Framework\TestCase;
use ProtoReflection\Lexer\Protobuf\ProtoLexer;
use ProtoReflection\Lexer\Protobuf\TokenType;
use ProtoReflection\Parser\ProtobufGrammar;

class ParserTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testParse(): void
    {
        self::expectNotToPerformAssertions();

        $content = file_get_contents(__DIR__ . '/../Fixtures/complex.proto');

        $lexer = new ProtoLexer();
        $lexer->setInput($content);

        $tokens = [];

        while ($token = $lexer->moveNext()) {
            if ($token->getType() === TokenType::T_LINE_COMMENT->value || $token->getType() === TokenType::T_BLOCK_COMMENT->value) {
                continue;
            }

            $tokens[] = $token;
        }

        $grammar = new ProtobufGrammar();
        $ast = $grammar->getAstBuilder()->generateAST($tokens);

        die((string) $ast->getAST());
    }
}
