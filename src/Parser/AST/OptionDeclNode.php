<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\AST;

readonly class OptionDeclNode
{
    public function __construct(
        public string $name,
        public ?string $value
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
