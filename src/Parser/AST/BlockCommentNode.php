<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\AST;

readonly class BlockCommentNode
{
    public function __construct(private string $comment)
    {
    }

    public function getContent(): string
    {
        return $this->comment;
    }
}
