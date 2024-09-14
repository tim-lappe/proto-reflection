<?php

declare(strict_types=1);

namespace ProtoReflection\Parser;

class StringUtils
{
    public static function stripQuotes(string $value): string
    {
        return trim($value, '"');
    }
}
