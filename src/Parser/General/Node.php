<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General;

use ProtoReflection\Parser\General\Result;

interface Node
{
    public function getChild(int $index): ?Node;
}

