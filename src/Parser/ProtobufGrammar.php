<?php

declare(strict_types=1);

namespace ProtoReflection\Parser;

use ProtoReflection\Lexer\Protobuf\TokenType;
use ProtoReflection\Parser\General\ASTBuilder;
use ProtoReflection\Parser\General\Rules\Chain;
use ProtoReflection\Parser\General\Rules\LazyRule;
use ProtoReflection\Parser\General\Rules\MatchType;
use ProtoReflection\Parser\General\Rules\Optional;
use ProtoReflection\Parser\General\Rules\OrNode;
use ProtoReflection\Parser\General\Rules\Repeat;

class ProtobufGrammar 
{
    /**
     * @var array<array-key, ASTBuilder>
     */
    private array $builderCache = [];

    public function getAstBuilder(): ASTBuilder
    {
        return new Chain([
            $this->syntax(),
            new Repeat(new OrNode(
                rules: [
                    $this->importStatement(),
                    $this->packageStatement(), 
                    $this->optionStatement(),
                    $this->topLevelDef(),
                    $this->emptyStatement()
                ],
            ), min: 0, key: 'STATEMENTS'),
        ]);
    }

    /**
     * @param string $key
     * @param callable(): ASTBuilder $build
     * @return ASTBuilder
     */
    private function get(string $key, callable $build): ASTBuilder
    {
        if (isset($this->builderCache[$key])) {
            return $this->builderCache[$key];
        }

        $builder = $build();
        $this->builderCache[$key] = $builder;

        return $builder;
    }

    public function syntax(): ASTBuilder
    {
        return $this->get('SYNTAX', static fn() => new Chain([
            new MatchType(TokenType::T_SYNTAX->value),
            new MatchType(TokenType::T_EQUALS->value),
            new OrNode([
                new MatchType(TokenType::T_PROTO3_LIT_SINGLE->value),
                new MatchType(TokenType::T_PROTO3_LIT_DOUBLE->value),
            ]),
            new MatchType(TokenType::T_SEMICOLON->value)
        ], key: 'SYNTAX'));
    }

    public function importStatement(): ASTBuilder
    {
        return $this->get('IMPORT_STATEMENT', fn() => new Chain([
            new MatchType(TokenType::T_IMPORT->value),
            new Optional(new OrNode([
                new MatchType(TokenType::T_WEAK->value),
                new MatchType(TokenType::T_PUBLIC->value),
            ])),
            $this->strLit(),
            new MatchType(TokenType::T_SEMICOLON->value)
        ], key: 'IMPORT_STATEMENT'));
    }

    public function packageStatement(): ASTBuilder
    {
        return $this->get('PACKAGE_STATEMENT', fn() => new Chain([
            new MatchType(TokenType::T_PACKAGE->value),
            $this->fullIdentifier(),
            new MatchType(TokenType::T_SEMICOLON->value)
        ], key: 'PACKAGE_STATEMENT'));
    }

    public function optionStatement(): ASTBuilder
    {
        return $this->get('OPTION_STATEMENT', fn() => new Chain([
            new MatchType(TokenType::T_OPTION->value),
            $this->optionName(),
            new MatchType(TokenType::T_EQUALS->value),
            $this->constant(),
            new MatchType(TokenType::T_SEMICOLON->value)
        ], key: 'OPTION_STATEMENT'));
    }

    public function topLevelDef(): ASTBuilder
    {
        return $this->get('TOP_LEVEL_DEF', fn() => new OrNode([
            'MESSAGE_DEF' => $this->messageDef(),
            'ENUM_DEF' => $this->enumDef(),
            'EXTEND_DEF' => $this->extendDef(),
            'SERVICE_DEF' => $this->serviceDef(),
        ], key: 'TOP_LEVEL_DEF'));
    }

    public function messageDef(): ASTBuilder
    {
        return $this->get('MESSAGE_DEF', fn() => new Chain([
            new MatchType(TokenType::T_MESSAGE->value),
            new MatchType(TokenType::T_IDENTIFIER->value),
            $this->messageBody()
        ], key: 'MESSAGE_DEF'));
    }

    public function messageBody(): ASTBuilder
    {
        return $this->get('MESSAGE_BODY', fn() => new Chain([
            new MatchType(TokenType::T_LEFT_BRACE->value),
            new Repeat(new OrNode([
                'FIELD' => $this->field(),
                'ENUM_DEF' => $this->enumDef(),
                'MESSAGE_DEF' => new LazyRule(fn() => $this->messageDef()),
                'EXTEND_DEF' => $this->extendDef(),
                'OPTION_STATEMENT' => $this->optionStatement(),
                'ONEOF' => $this->oneof(),
                'MAP_FIELD' => $this->mapField(),
                'RESERVED' => $this->reserved(),
                'EMPTY_STATEMENT' => $this->emptyStatement()
            ]), 0, key: 'STATEMENTS'),
            new MatchType(TokenType::T_RIGHT_BRACE->value)
        ], key: 'MESSAGE_BODY'));
    }

    public function oneof(): ASTBuilder
    {
        return $this->get('ONEOF', fn() =>  new Chain([
            new MatchType(TokenType::T_ONEOF->value),
            new MatchType(TokenType::T_IDENTIFIER->value),
            new MatchType(TokenType::T_LEFT_BRACE->value),
            new Repeat(new OrNode([
                'OPTION_STATEMENT' => $this->optionStatement(),
                'ONEOF_FIELD' => $this->oneofField(),
                'EMPTY_STATEMENT' => $this->emptyStatement()
            ]), 0, key: 'STATEMENTS'),
            new MatchType(TokenType::T_RIGHT_BRACE->value)
        ], key: 'ONEOF'));
    }

    public function oneofField(): ASTBuilder
    {
        return $this->get('ONEOF_FIELD', fn() => new Chain([
            $this->type(),
            new MatchType(TokenType::T_IDENTIFIER->value),
            new MatchType(TokenType::T_EQUALS->value),
            new MatchType(TokenType::T_INT_LITERAL->value),
            new Optional(new Chain([
                new MatchType(TokenType::T_LEFT_BRACKET->value),
                $this->fieldOptions(),
                new MatchType(TokenType::T_RIGHT_BRACKET->value)
            ])),
            new MatchType(TokenType::T_SEMICOLON->value)
        ], key: 'ONEOF_FIELD'));
    }

    public function mapField(): ASTBuilder
    {
        return $this->get('MAP_FIELD', fn() => new Chain([
            new MatchType(TokenType::T_MAP->value),
            new MatchType(TokenType::T_LEFT_ANGLE->value),
            $this->keyType(),
            new MatchType(TokenType::T_COMMA->value),
            $this->type(),
            new MatchType(TokenType::T_RIGHT_ANGLE->value),
            new MatchType(TokenType::T_IDENTIFIER->value),
            new MatchType(TokenType::T_EQUALS->value),
            new MatchType(TokenType::T_INT_LITERAL->value),
            new Optional(new Chain([
                new MatchType(TokenType::T_LEFT_BRACKET->value),
                $this->fieldOptions(),
                new MatchType(TokenType::T_RIGHT_BRACKET->value)
            ])),
            new MatchType(TokenType::T_SEMICOLON->value)
        ], key: 'MAP_FIELD'));
    }

    public function keyType(): ASTBuilder
    {
        return $this->get('KEY_TYPE', fn() => new OrNode([
            new MatchType(TokenType::T_INT32->value),
            new MatchType(TokenType::T_INT64->value), 
            new MatchType(TokenType::T_UINT32->value),
            new MatchType(TokenType::T_UINT64->value),
            new MatchType(TokenType::T_SINT32->value),
            new MatchType(TokenType::T_SINT64->value),
            new MatchType(TokenType::T_FIXED32->value),
            new MatchType(TokenType::T_FIXED64->value),
            new MatchType(TokenType::T_SFIXED32->value),
            new MatchType(TokenType::T_SFIXED64->value),
            new MatchType(TokenType::T_BOOL->value),
            new MatchType(TokenType::T_STRING->value)
        ], key: 'KEY_TYPE'));
    }

    public function reserved(): ASTBuilder
    {
        return $this->get('RESERVED', fn() => new Chain([
            new MatchType(TokenType::T_RESERVED->value),
            new OrNode([
                'RANGES' => $this->ranges(),
                'FIELD_NAMES' => $this->reservedFieldNames()
            ]),
            new MatchType(TokenType::T_SEMICOLON->value)
        ], key: 'RESERVED'));
    }

    public function ranges(): ASTBuilder
    {
        return $this->get('RANGES', fn() => new Chain([
            $this->range(),
            new Repeat(new Chain([
                new MatchType(TokenType::T_COMMA->value),
                $this->range()
            ]), min: 0, key: 'RANGES'),
        ], key: 'RANGES'));
    }

    public function range(): ASTBuilder
    {
        return $this->get('RANGE', fn() => new Chain([
            new MatchType(TokenType::T_INT_LITERAL->value),
            new Optional(new Chain([
                new MatchType(TokenType::T_TO->value),
                new OrNode([
                    new MatchType(TokenType::T_INT_LITERAL->value),
                    new MatchType(TokenType::T_MAX->value)
                ])
            ]))
        ], key: 'RANGE'));
    }

    public function reservedFieldNames(): ASTBuilder
    {
        return $this->get('RESERVED_FIELD_NAMES', fn() => new Chain([
            $this->strLit(),
            new Repeat(new Chain([
                new MatchType(TokenType::T_COMMA->value),
                $this->strLit()
            ]), min: 0, key: 'FIELD_NAMES'),
        ], key: 'RESERVED_FIELD_NAMES'));
    }

    public function extendDef(): ASTBuilder
    {
        return $this->get('EXTEND_DEF', fn() => new Chain([
            new MatchType(TokenType::T_EXTEND->value),
            $this->messageType(),
            new MatchType(TokenType::T_LEFT_BRACE->value),
            new Repeat(new OrNode([
                'FIELD' => $this->field(),
                'EMPTY' => $this->emptyStatement()
            ]), min: 0, key: 'FIELDS'),
            new MatchType(TokenType::T_RIGHT_BRACE->value)
        ], key: 'EXTEND_DEF'));
    }

    public function serviceDef(): ASTBuilder
    {
        return $this->get('SERVICE_DEF', fn() => new Chain([
            new MatchType(TokenType::T_SERVICE->value),
            new MatchType(TokenType::T_IDENTIFIER->value),
            new MatchType(TokenType::T_LEFT_BRACE->value),
            new Repeat(new OrNode([
                'OPTION' => $this->optionStatement(),
                'RPC' => $this->rpc(),
                'EMPTY' => $this->emptyStatement()
            ]), min: 0, key: 'STATEMENTS'),
            new MatchType(TokenType::T_RIGHT_BRACE->value)
        ], key: 'SERVICE_DEF'));
    }

    public function rpc(): ASTBuilder
    {
        return $this->get('RPC', fn() => new Chain([
            new MatchType(TokenType::T_RPC->value),
            new MatchType(TokenType::T_IDENTIFIER->value),
            new MatchType(TokenType::T_LEFT_PAREN->value),
            new Optional(new MatchType(TokenType::T_STREAM->value)),
            $this->messageType(),
            new MatchType(TokenType::T_RIGHT_PAREN->value),
            new MatchType(TokenType::T_RETURNS->value),
            new MatchType(TokenType::T_LEFT_PAREN->value),
            new Optional(new MatchType(TokenType::T_STREAM->value)),
            $this->messageType(),
            new MatchType(TokenType::T_RIGHT_PAREN->value),
            new OrNode([
                new Chain([
                    new MatchType(TokenType::T_LEFT_BRACE->value),
                    new Repeat(new OrNode([
                        $this->optionStatement(),
                        $this->emptyStatement()
                    ]), min: 0, key: 'STATEMENTS'),
                    new MatchType(TokenType::T_RIGHT_BRACE->value)
                ]),
                new MatchType(TokenType::T_SEMICOLON->value)
            ])
        ], key: 'RPC'));
    }

    public function enumDef(): ASTBuilder
    {
        return $this->get('ENUM_DEF', fn() => new Chain([
            new MatchType(TokenType::T_ENUM->value),
            new MatchType(TokenType::T_IDENTIFIER->value),
            'BODY' => $this->enumBody()
        ], key: 'ENUM_DEF'));
    }

    public function enumBody(): ASTBuilder
    {
        return $this->get('ENUM_BODY', fn() => new Chain([
            new MatchType(TokenType::T_LEFT_BRACE->value),
            new Repeat(new OrNode([
                'OPTION' => $this->optionStatement(),
                'FIELD' => $this->enumField(),
                'EMPTY' => $this->emptyStatement()
            ]), min: 0, key: 'STATEMENTS'),
            new MatchType(TokenType::T_RIGHT_BRACE->value)
        ], key: 'ENUM_BODY'));
    }

    public function enumField(): ASTBuilder
    {
        return $this->get('ENUM_FIELD', fn() => new Chain([
            'NAME' => new MatchType(TokenType::T_IDENTIFIER->value),
            'EQUALS' => new MatchType(TokenType::T_EQUALS->value),
            'VALUE' => new Chain([
                new Optional(new MatchType(TokenType::T_MINUS->value)),
                new MatchType(TokenType::T_INT_LITERAL->value),
            ]),
            'OPTIONS' => new Optional($this->enumValueOptions()),
            'SEMICOLON' => new MatchType(TokenType::T_SEMICOLON->value),
        ], key: 'ENUM_FIELD'));
    }

    public function enumValueOptions(): ASTBuilder
    {
        return $this->get('ENUM_VALUE_OPTIONS', fn() => new Chain([
            new MatchType(TokenType::T_LEFT_BRACKET->value),
            new Chain([
                $this->enumValueOption(),
                new Repeat(new Chain([
                    new MatchType(TokenType::T_COMMA->value),
                    $this->enumValueOption()
                ]), min: 0, key: 'OPTIONS'),
            ]),
            new MatchType(TokenType::T_RIGHT_BRACKET->value)
        ], key: 'ENUM_VALUE_OPTIONS'));
    }

    public function enumValueOption(): ASTBuilder
    {
        return $this->get('ENUM_VALUE_OPTION', fn() => new Chain([
            $this->optionName(),
            new MatchType(TokenType::T_EQUALS->value),
            $this->constant()
        ], key: 'ENUM_VALUE_OPTION'));
    }

    public function field(): ASTBuilder
    {
        return $this->get('FIELD', fn() => new Chain([
            'LABEL' => new Optional($this->fieldLabel()),
            'TYPE' => $this->type(),
            'NAME' => new MatchType(TokenType::T_IDENTIFIER->value),
            'EQUALS' => new MatchType(TokenType::T_EQUALS->value),
            'DEFAULT_VALUE' => new MatchType(TokenType::T_INT_LITERAL->value),
            'OPTIONS' => new Optional(new Chain([
                new MatchType(TokenType::T_LEFT_BRACKET->value),
                $this->fieldOptions(),
                new MatchType(TokenType::T_RIGHT_BRACKET->value)
            ])),
            new MatchType(TokenType::T_SEMICOLON->value)
        ], key: 'FIELD'));
    }

    public function fieldOptions(): ASTBuilder
    {
        return $this->get('FIELD_OPTIONS', fn() => new Chain([
            $this->fieldOption(),
            new Repeat(new Chain([
                new MatchType(TokenType::T_COMMA->value),
                $this->fieldOption()
            ]), min: 0, key: 'OPTIONS'),
        ], key: 'FIELD_OPTIONS'));
    }

    public function fieldOption(): ASTBuilder
    {
        return $this->get('FIELD_OPTION', fn() => new Chain([
            $this->optionName(),
            new MatchType(TokenType::T_EQUALS->value),
            $this->constant()
        ], key: 'FIELD_OPTION'));
    }

    public function type(): ASTBuilder
    {
        return $this->get('TYPE', fn() => new OrNode([
            new MatchType(TokenType::T_DOUBLE->value),
            new MatchType(TokenType::T_FLOAT->value), 
            new MatchType(TokenType::T_INT32->value),
            new MatchType(TokenType::T_INT64->value),
            new MatchType(TokenType::T_UINT32->value),
            new MatchType(TokenType::T_UINT64->value),
            new MatchType(TokenType::T_SINT32->value),
            new MatchType(TokenType::T_SINT64->value),
            new MatchType(TokenType::T_FIXED32->value),
            new MatchType(TokenType::T_FIXED64->value),
            new MatchType(TokenType::T_SFIXED32->value),
            new MatchType(TokenType::T_SFIXED64->value),
            new MatchType(TokenType::T_BOOL->value),
            new MatchType(TokenType::T_STRING->value),
            new MatchType(TokenType::T_BYTES->value),
            $this->messageType(),
            $this->enumType()
        ], key: 'TYPE'));
    }

    public function messageType(): ASTBuilder
    {
        return $this->get('MESSAGE_TYPE', fn() => new Chain([
            new Optional(new MatchType(TokenType::T_DOT->value)),
            new Repeat(new Chain([
                new MatchType(TokenType::T_IDENTIFIER->value),
                new MatchType(TokenType::T_DOT->value)
            ]), min: 0),
            new MatchType(TokenType::T_IDENTIFIER->value)
        ], key: 'MESSAGE_TYPE'));
    }

    public function enumType(): ASTBuilder
    {
        return $this->get('ENUM_TYPE', fn() => new Chain([
            new Optional(new MatchType(TokenType::T_DOT->value)),
            new Optional(new Repeat(new Chain([
                new MatchType(TokenType::T_IDENTIFIER->value),
                new MatchType(TokenType::T_DOT->value)
            ]), min: 0)),
            $this->enumName()
        ], key: 'ENUM_TYPE'));
    }

    public function enumName(): ASTBuilder
    {
        return $this->get('ENUM_NAME', fn() => new MatchType(TokenType::T_IDENTIFIER->value));
    }

    public function fieldLabel(): ASTBuilder
    {
        return $this->get('FIELD_LABEL', fn() => new OrNode([
            new MatchType(TokenType::T_OPTIONAL->value),
            new MatchType(TokenType::T_REPEATED->value),
        ], key: 'FIELD_LABEL'));
    }

    public function constant(): ASTBuilder
    {
        return $this->get('CONSTANT', fn() => new OrNode([
            $this->fullIdentifier(),
            $this->intLit(),
            $this->floatLit(),
            $this->strLit(),
            $this->boolLit()
        ], key: 'CONSTANT'));
    }

    public function optionName(): ASTBuilder
    {
        return $this->get('OPTION_NAME', fn() => new OrNode([
            $this->fullIdentifier(),
            new Chain([
                new MatchType(TokenType::T_LEFT_PAREN->value),
                $this->fullIdentifier(),
                new MatchType(TokenType::T_RIGHT_PAREN->value),
                new Optional(new Chain([
                    new MatchType(TokenType::T_DOT->value),
                    new MatchType(TokenType::T_IDENTIFIER->value),
                ]))
            ])
        ], key: 'OPTION_NAME'));
    }

    public function fullIdentifier(): ASTBuilder
    {
        return $this->get('FULL_IDENTIFIER', fn() => new Chain([
            new MatchType(TokenType::T_IDENTIFIER->value),
            new Repeat(new Chain([
                new MatchType(TokenType::T_DOT->value),
                new MatchType(TokenType::T_IDENTIFIER->value),
            ]), min: 0),
        ], key: 'FULL_IDENTIFIER'));
    }

    public function floatLit(): ASTBuilder
    {
        return $this->get('FLOAT_LIT', fn() => new Chain([
            new Optional(new OrNode([
                new MatchType(TokenType::T_MINUS->value),
                new MatchType(TokenType::T_PLUS->value),
            ])),
            new MatchType(TokenType::T_FLOAT_LITERAL->value),
        ], key: 'FLOAT_LIT'));
    }

    public function intLit(): ASTBuilder
    {
        return $this->get('INT_LIT', fn() => new Chain([
            new Optional(new OrNode([
                new MatchType(TokenType::T_MINUS->value),
                new MatchType(TokenType::T_PLUS->value),
            ])),
            new MatchType(TokenType::T_INT_LITERAL->value),
        ], key: 'INT_LIT'));
    }

    public function strLit(): ASTBuilder
    {
        return $this->get('STR_LIT', fn() => new OrNode([
            new MatchType(TokenType::T_STRING_LITERAL->value),
            new MatchType(TokenType::T_PROTO3_LIT_SINGLE->value),
            new MatchType(TokenType::T_PROTO3_LIT_DOUBLE->value)
        ], key: 'STR_LIT'));
    }

    public function boolLit(): ASTBuilder
    {
        return $this->get('BOOL_LIT', fn() => new MatchType(TokenType::T_BOOL->value));
    }

    public function emptyStatement(): ASTBuilder
    {
        return $this->get('EMPTY_STATEMENT', fn() => new MatchType(TokenType::T_SEMICOLON->value));
    }
}
