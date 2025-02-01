<?php

declare(strict_types=1);

namespace ProtoReflection\Lexer\G4;

enum TokenType: string
{
    case T_GRAMMAR = 'GRAMMAR';
    case T_PARSER = 'PARSER';
    case T_LEXER = 'LEXER';
    case T_FRAGMENT = 'FRAGMENT';
    case T_IMPORT = 'IMPORT';
    case T_OPTIONS = 'OPTIONS';
    case T_RETURNS = 'RETURNS';
    case T_LOCALS = 'LOCALS';
    case T_THROWS = 'THROWS';
    case T_CATCH = 'CATCH';
    case T_FINALLY = 'FINALLY';
    case T_MODE = 'MODE';
    case T_SKIP = 'SKIP';
    case T_MORE = 'MORE';
    case T_PUSH_MODE = 'PUSH_MODE';
    case T_POP_MODE = 'POP_MODE';
    case T_TYPE = 'TYPE';
    case T_CHANNEL = 'CHANNEL';
    case T_SEMICOLON = 'SEMICOLON';
    case T_COLON = 'COLON';
    case T_DOUBLE_COLON = 'DOUBLE_COLON';
    case T_ARROW = 'ARROW';
    case T_EQUALS = 'EQUALS';
    case T_PIPE = 'PIPE';
    case T_STAR = 'STAR';
    case T_PLUS = 'PLUS';
    case T_QUESTION = 'QUESTION';
    case T_DOT = 'DOT';
    case T_RANGE = 'RANGE';
    case T_AT = 'AT';
    case T_HASH = 'HASH';
    case T_TILDE = 'TILDE';
    case T_NOT = 'NOT';
    case T_LEFT_BRACE = 'L_BRACE';
    case T_RIGHT_BRACE = 'R_BRACE';
    case T_LEFT_PAREN = 'L_PAREN';
    case T_RIGHT_PAREN = 'R_PAREN';
    case T_LEFT_BRACKET = 'L_BRACKET';
    case T_RIGHT_BRACKET = 'R_BRACKET';
    case T_LEFT_ANGLE = 'L_ANGLE';
    case T_RIGHT_ANGLE = 'R_ANGLE';
    case T_COMMA = 'COMMA';
    case T_DOLLAR = 'DOLLAR';
    case T_IDENTIFIER = 'IDENTIFIER';
    case T_COMMENT = 'COMMENT';
    case T_NONE = 'NONE';
    case T_EOF = 'EOF';
}
