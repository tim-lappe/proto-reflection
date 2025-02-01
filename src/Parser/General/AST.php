<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General;

use ProtoReflection\Parser\General\Node;

final class AST implements Node
{
    /**
     * @var list<Node>
     */
    private array $children = [];

    /**
     * @var string|null
     */
    private ?string $value = null;

    private string|int|null $key = null;

    public function addChild(Node $node): self
    {
        $this->children[] = $node;
        
        return $this;
    }

    public function getChild(int $index): ?Node 
    {
        return $this->children[$index] ?? null;
    }

    /**
     * @return array<array-key, Node>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function setKey(string|int|null $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): string|int|null
    {
        return $this->key;
    }

    public function merge(AST $ast): self
    {
        $this->children = array_values(array_merge($this->children, $ast->getChildren()));

        return $this;
    }

    public static function encapsulate(string|int|null $key, AST $ast): self
    {
        if ($ast->getKey() === null || $ast->getKey() === '') {
            $ast->setKey($key);
            return $ast;
        }

        $encapsulated = new self();
        $encapsulated->setKey($key);
        $encapsulated->addChild($ast);

        return $encapsulated;
    }

    public function __toString(): string
    {
        return $this->print(0);
    }

    public function print(int $indent = 0): string
    {
        $string = str_repeat('|  ', $indent) . "+-- {$this->key}" . ($this->value !== null ? ": {$this->value}" : '') . " \n";
        foreach ($this->children as $child) {
            if ($child instanceof AST) {
                $string .= $child->print($indent + 1);
            }
        }

        return $string;
    }
}
