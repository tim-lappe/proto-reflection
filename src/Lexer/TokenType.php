<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer;

use function Symfony\Component\String\s;

enum TokenType: string
{
    case T_SYNTAX = 'SYNTAX';
    case T_EDITION = 'EDITION';
    case T_IMPORT = 'IMPORT';
    case T_WEAK = 'WEAK';
    case T_PUBLIC = 'PUBLIC';
    case T_PACKAGE = 'PACKAGE';
    case T_OPTION = 'OPTION';
    case T_INF = 'INF';
    case T_NAN = 'NAN';
    case T_REPEATED = 'REPEATED';
    case T_OPTIONAL = 'OPTIONAL';
    case T_REQUIRED = 'REQUIRED';
    case T_BOOL = 'BOOL';
    case T_STRING = 'STRING';
    case T_BYTES = 'BYTES';
    case T_FLOAT = 'FLOAT';
    case T_DOUBLE = 'DOUBLE';
    case T_INT32 = 'INT32';
    case T_INT64 = 'INT64';
    case T_UINT32 = 'UINT32';
    case T_UINT64 = 'UINT64';
    case T_SINT32 = 'SINT32';
    case T_SINT64 = 'SINT64';
    case T_FIXED32 = 'FIXED32';
    case T_FIXED64 = 'FIXED64';
    case T_SFIXED32 = 'SFIXED32';
    case T_SFIXED64 = 'SFIXED64';
    case T_GROUP = 'GROUP';
    case T_ONEOF = 'ONEOF';
    case T_MAP = 'MAP';
    case T_EXTENSIONS = 'EXTENSIONS';
    case T_TO = 'TO';
    case T_MAX = 'MAX';
    case T_RESERVED = 'RESERVED';
    case T_ENUM = 'ENUM';
    case T_MESSAGE = 'MESSAGE';
    case T_EXTEND = 'EXTEND';
    case T_SERVICE = 'SERVICE';
    case T_RPC = 'RPC';
    case T_STREAM = 'STREAM';
    case T_RETURNS = 'RETURNS';

    case T_IDENTIFIER = 'IDENTIFIER';
    case T_INT_LITERAL = 'INT_LITERAL';
    case T_FLOAT_LITERAL = 'FLOAT_LITERAL';
    case T_STRING_LITERAL = 'STRING_LITERAL';

    case T_SEMICOLON = 'SEMICOLON';
    case T_COMMA = 'COMMA';
    case T_DOT = 'DOT';
    case T_SLASH = 'SLASH';
    case T_COLON = 'COLON';
    case T_EQUALS = 'EQUALS';
    case T_MINUS = 'MINUS';
    case T_PLUS = 'PLUS';
    case T_LEFT_PAREN = 'L_PAREN';
    case T_RIGHT_PAREN = 'R_PAREN';
    case T_LEFT_BRACE = 'L_BRACE';
    case T_RIGHT_BRACE = 'R_BRACE';
    case T_LEFT_BRACKET = 'L_BRACKET';
    case T_RIGHT_BRACKET = 'R_BRACKET';
    case T_LEFT_ANGLE = 'L_ANGLE';
    case T_RIGHT_ANGLE = 'R_ANGLE';
    case T_LINE_COMMENT = 'LINE_COMMENT';
    case T_BLOCK_COMMENT = 'BLOCK_COMMENT';

    public function isFieldCardinality(): bool
    {
        return in_array($this, self::getFieldCardinalityTokens(), true);
    }

    /**
     * @return array<TokenType>
     */
    public static function getFieldCardinalityTokens(): array
    {
        return [
            self::T_OPTIONAL,
            self::T_REQUIRED,
            self::T_REPEATED
        ];
    }


    public function isScalarType(): bool
    {
        return in_array($this, self::getScalarValueTypes(), true);
    }

    /**
     * @return array<TokenType>
     */
    public static function getScalarValueTypes(): array
    {
        return [
            self::T_STRING_LITERAL,
            self::T_INT_LITERAL,
            self::T_FLOAT_LITERAL
        ];
    }
}
