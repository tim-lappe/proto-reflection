<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General\Provider\G4;

use ProtoReflection\Lexer\G4\G4Lexer;
use ProtoReflection\Parser\General\ASTBuilder;

final class G4File
{
    private string $content;

    /** @var array<string,string> */
    private array $productions = [];

    public function __construct(private readonly string $filePath)
    {
        $this->content = $this->readFile();
    }

    private function readFile(): string
    {
        $content = file_get_contents($this->filePath);
        if ($content === false) {
            throw new \RuntimeException('Failed to read file: ' . $this->filePath);
        }

        return $content;
    }

    public function getProductions(): array
    {
        return $this->productions;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
