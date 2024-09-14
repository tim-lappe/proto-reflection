<?php

declare(strict_types=1);

namespace ProtoReflection\Parser;

use Exception;
use ProtoReflection\Lexer\ProtoLexerInterface;
use ProtoReflection\Parser\AST\ProtoNode;
use ProtoReflection\Parser\Step\Message\MessageDecl;
use ProtoReflection\Parser\Step\OptionDecl;
use ProtoReflection\Parser\Step\PackageDecl;
use ProtoReflection\Parser\Step\SyntaxDecl;

readonly class Parser
{
    public function __construct(
        private string              $content,
        private ProtoLexerInterface $lexer
    ) {
        $this->lexer->setInput($this->content);
    }

    /**
     * @throws Exception
     */
    public function parse(): ProtoNode
    {
        $this->lexer->moveNext();

        $protoNode = new ProtoNode();
        $fileElements = [];
        $syntaxDeclParser = new SyntaxDecl($this->lexer);
        if ($syntaxDeclParser->match()) {
            $protoNode->setSyntaxNode($syntaxDeclParser->parseSyntax());
        }

        $packageDeclParser = new PackageDecl($this->lexer);
        $optionDeclParser = new OptionDecl($this->lexer);
        $messageDeclParser = new MessageDecl($this->lexer);

        while ($this->lexer->moveNext()) {
            if ($packageDeclParser->match()) {
                $fileElements[] = $packageDeclParser->parsePackage();
            }

            if ($optionDeclParser->match()) {
                $fileElements[] = $optionDeclParser->parseOption();
            }

            if ($messageDeclParser->match()) {
                $fileElements[] = $messageDeclParser->parseMessage();
            }
        }

        $protoNode->setFileElements($fileElements);

        return $protoNode;
    }
}
