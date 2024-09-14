<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\AST;

readonly class PackageDeclNode
{
    public function __construct(private string $packageIdentifier)
    {
    }

    public function getPackageIdentifier(): string
    {
        return $this->packageIdentifier;
    }
}
