<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\AST;

readonly class MessageFieldDeclNode
{
    public function __construct(
        private string $fieldCardinality,
        private string $fieldDeclType,
        private string $fieldName,
        private int $fieldNumber
    ) {
    }

    public function getFieldCardinality(): string
    {
        return $this->fieldCardinality;
    }

    public function getFieldDeclType(): string
    {
        return $this->fieldDeclType;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getFieldNumber(): int
    {
        return $this->fieldNumber;
    }
}
