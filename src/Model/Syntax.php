<?php

declare(strict_types=1);

namespace ProtoReflection\Model;

enum Syntax: string
{
    case PROTO2 = 'proto2';
    case PROTO3 = 'proto3';
}
