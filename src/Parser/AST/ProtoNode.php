<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\AST;

class ProtoNode
{
    /**
     * @param SyntaxNode|null $syntaxNode
     * @param array<ImportDeclNode|PackageDeclNode> $fileElements
     */
    public function __construct(
        private ?SyntaxNode $syntaxNode = null,
        private array $fileElements = [],
    ) {
    }

    /**
     * @return array<ImportDeclNode|PackageDeclNode>
     */
    public function getFileElements(): array
    {
        return $this->fileElements;
    }

    public function getSyntaxNode(): ?SyntaxNode
    {
        return $this->syntaxNode;
    }

    public function setSyntaxNode(?SyntaxNode $syntaxNode): ProtoNode
    {
        $this->syntaxNode = $syntaxNode;

        return $this;
    }

    public function setFileElements(array $fileElements): ProtoNode
    {
        $this->fileElements = $fileElements;

        return $this;
    }
}
