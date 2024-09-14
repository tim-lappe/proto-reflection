<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer;

use Doctrine\Common\Lexer\AbstractLexer;

/**
 * Class Lexer
 * @extends AbstractLexer<TokenType>
 * @internal This class is not intended to be used or overwritten by developers.
 */
class DoctrineProtoLexer extends AbstractLexer
{
    protected array $tokens = [
        'syntax' => TokenType::T_SYNTAX,
        'edition' => TokenType::T_EDITION,
        'import' => TokenType::T_IMPORT,
        'weak' => TokenType::T_WEAK,
        'public' => TokenType::T_PUBLIC,
        'package' => TokenType::T_PACKAGE,
        'option' => TokenType::T_OPTION,
        'inf' => TokenType::T_INF,
        'nan' => TokenType::T_NAN,
        'repeated' => TokenType::T_REPEATED,
        'optional' => TokenType::T_OPTIONAL,
        'required' => TokenType::T_REQUIRED,
        'bool' => TokenType::T_BOOL,
        'string' => TokenType::T_STRING,
        'bytes' => TokenType::T_BYTES,
        'float' => TokenType::T_FLOAT,
        'double' => TokenType::T_DOUBLE,
        'int32' => TokenType::T_INT32,
        'int64' => TokenType::T_INT64,
        'uint32' => TokenType::T_UINT32,
        'uint64' => TokenType::T_UINT64,
        'sint32' => TokenType::T_SINT32,
        'sint64' => TokenType::T_SINT64,
        'fixed32' => TokenType::T_FIXED32,
        'fixed64' => TokenType::T_FIXED64,
        'sfixed32' => TokenType::T_SFIXED32,
        'sfixed64' => TokenType::T_SFIXED64,
        'group' => TokenType::T_GROUP,
        'oneof' => TokenType::T_ONEOF,
        'map' => TokenType::T_MAP,
        'extensions' => TokenType::T_EXTENSIONS,
        'to' => TokenType::T_TO,
        'max' => TokenType::T_MAX,
        'reserved' => TokenType::T_RESERVED,
        'enum' => TokenType::T_ENUM,
        'message' => TokenType::T_MESSAGE,
        'extend' => TokenType::T_EXTEND,
        'service' => TokenType::T_SERVICE,
        'rpc' => TokenType::T_RPC,
        'stream' => TokenType::T_STREAM,
        'returns' => TokenType::T_RETURNS,
        ';' => TokenType::T_SEMICOLON,
        ',' => TokenType::T_COMMA,
        '.' => TokenType::T_DOT,
        '/' => TokenType::T_SLASH,
        ':' => TokenType::T_COLON,
        '=' => TokenType::T_EQUALS,
        '-' => TokenType::T_MINUS,
        '+' => TokenType::T_PLUS,
        '(' => TokenType::T_LEFT_PAREN,
        ')' => TokenType::T_RIGHT_PAREN,
        '{' => TokenType::T_LEFT_BRACE,
        '}' => TokenType::T_RIGHT_BRACE,
        '[' => TokenType::T_LEFT_BRACKET,
        ']' => TokenType::T_RIGHT_BRACKET,
        '<' => TokenType::T_LEFT_ANGLE,
        '>' => TokenType::T_RIGHT_ANGLE,
    ];

    /**
     * @return array<string>
     */
    protected function getCatchablePatterns(): array
    {
        return [
            '\[',
            '\(',
            '\)',
            '\]',
            '\;',
            '0[xX][0-9A-Fa-f]+',
            '0[0-7]+',
            '\d+\.\d*(?:[eE][+-]?\d+)?',
            '\d+(?:[eE][+-]?\d+)',
            '\.\d+(?:[eE][+-]?\d+)?',
            '\d+',
            "'(?:[^'\\\\]|\\\\.)*'",
            '"(?:[^"\\\\]|\\\\.)*"',
            '[A-Za-z_][A-Za-z0-9_]*',
            '\/\/.*',
            '\/\*.*?\*\/',
        ];
    }

    /**
     * @return array<string>
     */
    protected function getNonCatchablePatterns(): array
    {
        return [
           '\s+',
        ];
    }

    /**
     * @param string $value
     * @return TokenType|null
     */
    protected function getType(string &$value): ?TokenType
    {
        if (isset($this->tokens[strtolower($value)])) {
            return $this->tokens[strtolower($value)];
        }

        if ($value[0] === '/' && $value[1] === '/') {
            return TokenType::T_LINE_COMMENT;
        }

        if ($value[0] === '/' && $value[1] === '*') {
            return TokenType::T_BLOCK_COMMENT;
        }

        if (ctype_alpha($value[0]) || $value[0] === '_') {
            return TokenType::T_IDENTIFIER;
        }

        if (is_numeric($value)) {
            return TokenType::T_INT_LITERAL;
        }

        if (preg_match('/^0[xX][0-9A-Fa-f]+$/', $value)) {
            return TokenType::T_INT_LITERAL;
        }

        if (
            preg_match('/^\d+\.\d*(?:[eE][+-]?\d+)?$/', $value) ||
            preg_match('/^\d+(?:[eE][+-]?\d+)$/', $value) ||
            preg_match('/^\.\d+(?:[eE][+-]?\d+)?$/', $value)
        ) {
            return TokenType::T_FLOAT_LITERAL;
        }

        if ($value[0] === '"' || $value[0] === "'") {
            return TokenType::T_STRING_LITERAL;
        }

        return null;
    }
}
