<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\AST;

readonly class ImportDeclNode
{
    public function __construct(
        private string $importedFileName,
        private bool   $isWeak,
        private bool   $isPublic,
    ) {
    }

    public function getImportedFileName(): string
    {
        return $this->importedFileName;
    }

    public function isWeak(): bool
    {
        return $this->isWeak;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }
}
