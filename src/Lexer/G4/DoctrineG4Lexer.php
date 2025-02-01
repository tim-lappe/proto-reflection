<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer\G4;

use Doctrine\Common\Lexer\AbstractLexer;

/**
 * Class DoctrineG4Lexer
 * @extends AbstractLexer<TokenType, string>
 * @internal This class is not intended to be used or overwritten by developers.
 */
class DoctrineG4Lexer extends AbstractLexer
{
    /**
     * @var array<array-key, TokenType>
     */
    protected array $tokens = [
        'grammar' => TokenType::T_GRAMMAR,
        'parser' => TokenType::T_PARSER,
        'lexer' => TokenType::T_LEXER,
        'fragment' => TokenType::T_FRAGMENT,
        'import' => TokenType::T_IMPORT,
        'options' => TokenType::T_OPTIONS,
        'returns' => TokenType::T_RETURNS,
        'locals' => TokenType::T_LOCALS,
        'throws' => TokenType::T_THROWS,
        'catch' => TokenType::T_CATCH,
        'finally' => TokenType::T_FINALLY,
        'mode' => TokenType::T_MODE,
        'skip' => TokenType::T_SKIP,
        'more' => TokenType::T_MORE,
        'pushMode' => TokenType::T_PUSH_MODE,
        'popMode' => TokenType::T_POP_MODE,
        'type' => TokenType::T_TYPE,
        'channel' => TokenType::T_CHANNEL,
        ';' => TokenType::T_SEMICOLON,
        ':' => TokenType::T_COLON,
        '::' => TokenType::T_DOUBLE_COLON,
        '->' => TokenType::T_ARROW,
        '=' => TokenType::T_EQUALS,
        '|' => TokenType::T_PIPE,
        '*' => TokenType::T_STAR,
        '+' => TokenType::T_PLUS,
        '?' => TokenType::T_QUESTION,
        '.' => TokenType::T_DOT,
        '..' => TokenType::T_RANGE,
        '@' => TokenType::T_AT,
        '#' => TokenType::T_HASH,
        '~' => TokenType::T_TILDE,
        '!' => TokenType::T_NOT,
        '{' => TokenType::T_LEFT_BRACE,
        '}' => TokenType::T_RIGHT_BRACE,
        '(' => TokenType::T_LEFT_PAREN,
        ')' => TokenType::T_RIGHT_PAREN,
        '[' => TokenType::T_LEFT_BRACKET,
        ']' => TokenType::T_RIGHT_BRACKET,
        '<' => TokenType::T_LEFT_ANGLE,
        '>' => TokenType::T_RIGHT_ANGLE,
        ',' => TokenType::T_COMMA,
        '$' => TokenType::T_DOLLAR,
        'eof' => TokenType::T_EOF,
    ];

    protected function getCatchablePatterns(): array
    {
        return [
            '\[',
            '\]',
            '\(',
            '\)',
            '\{',
            '\}',
            '\;',
            '\:',
            '\:\:',
            '\-\>',
            '\=',
            '\|\*',
            '\+\*',
            '\?\*',
            '\.\.',
            '\@',
            '\#',
            '\~',
            '\!',
            '\<',
            '\>',
            '\,',
            '\$',
            '[A-Za-z_][A-Za-z0-9_]*',  // Identifiers with word boundaries
            '\/\/[^\r\n]*',            // Single line comments
            '\/\*[\s\S]*?\*\/',        // Multi line comments
        ];
    }

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
    protected function getType(&$value): ?TokenType
    {
        if (isset($this->tokens[strtolower($value)])) {
            return $this->tokens[strtolower($value)];
        }

        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $value)) {
            return TokenType::T_IDENTIFIER;
        }

        if (preg_match('/^\/\/[^\r\n]*$/', $value) || preg_match('/^\/\*[\s\S]*?\*\/$/', $value)) {
            return TokenType::T_COMMENT;
        }

        return TokenType::T_NONE;
    }
}
