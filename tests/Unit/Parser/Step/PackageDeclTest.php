<?php

declare(strict_types=1);

namespace ProtoReflection\Test\Unit\Parser\Step;

use Exception;
use ProtoReflection\Parser\Step\PackageDecl;

class PackageDeclTest extends ParserStepTestCase
{
    /**
     * @throws Exception
     *
     * @dataProvider correctPackageDeclProvider
     */
    public function testPackageDeclIsCorrect(string $input): void
    {
        self::expectNotToPerformAssertions();

        $syntaxDecl = new PackageDecl($this->createLexer($input));
        $syntaxDecl->parsePackage();
    }

    public static function correctPackageDeclProvider(): iterable
    {
        yield 'Simple package' => ['package test;'];
        yield 'Simple package with dots' => ['package test.package;'];
        yield 'Simple package with underscores' => ['package test_package;'];
        yield 'Simple package with numbers' => ['package test123;'];
    }

    /**
     * @dataProvider incorrectPackageDeclProvider
     * @param string $input
     * @throws Exception
     */
    public function testPackageDeclIsIncorrect(string $input): void
    {
        self::expectException(Exception::class);

        $syntaxDecl = new PackageDecl($this->createLexer($input));
        $syntaxDecl->parsePackage();
    }

    /**
     * @return iterable
     */
    public static function incorrectPackageDeclProvider(): iterable
    {
        yield 'Missing package keyword' => ['test;'];
        yield 'Missing semicolon' => ['package test'];
        yield 'Missing package name' => ['package;'];
        yield 'Missing semicolon after package name' => ['package test'];
    }
}
