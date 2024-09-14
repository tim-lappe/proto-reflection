<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\AST;

use ProtoReflection\Parser\AST\Declaration\OneOf;

readonly class CommentNode
{
    /**
     * @param OneOf<LineCommentNode, BlockCommentNode> $lineCommentOrBlockComment
     */
    public function __construct(
        private OneOf $lineCommentOrBlockComment,
    ) {
    }

    public function getContent(): string
    {
        return $this->lineCommentOrBlockComment->evaluate()->getContent();
    }
}
