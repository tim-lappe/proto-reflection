<?php

declare(strict_types=1);

namespace ProtoReflection\Model;

class FileDescription
{
    private Syntax $syntax;
    private string $package;
    private array $imports = [];
    private array $messages = [];
    private array $enums = [];
    private array $options = [];

    public function getSyntax(): Syntax
    {
        return $this->syntax;
    }

    public function setSyntax(Syntax $syntax): void
    {
        $this->syntax = $syntax;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function setPackage(string $package): void
    {
        $this->package = $package;
    }

    public function getImports(): array
    {
        return $this->imports;
    }

    public function setImports(array $imports): void
    {
        $this->imports = $imports;
    }

    public function addImport(string $import): void
    {
        $this->imports[] = $import;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    public function addMessage(MessageDescription $message): void
    {
        $this->messages[] = $message;
    }

    public function getEnums(): array
    {
        return $this->enums;
    }

    public function setEnums(array $enums): void
    {
        $this->enums = $enums;
    }

    public function addEnum(EnumDescription $enum): void
    {
        $this->enums[] = $enum;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function addOption(Option $option): void
    {
        $this->options[] = $option;
    }
}
