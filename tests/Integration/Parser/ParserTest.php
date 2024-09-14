<?php

declare(strict_types=1);

namespace ProtoReflection\Test\Integration\Parser;

use Exception;
use PHPUnit\Framework\TestCase;
use ProtoReflection\Lexer\ProtoLexer;
use ProtoReflection\Parser\Parser;

class ParserTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testParse(): void
    {
        self::expectNotToPerformAssertions();

        $content = file_get_contents(__DIR__ . '/../Fixtures/complex.proto');

        $parser = new Parser($content, new ProtoLexer());
        $ast = $parser->parse();

        var_dump($ast);
    }
}
