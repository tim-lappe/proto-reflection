<?php

declare(strict_types=1);

namespace ProtoReflection\Parser\General\Provider;

use ProtoReflection\Parser\General\ASTBuilder;

interface GrammarBuilder
{
    public function build(): ASTBuilder;
}
