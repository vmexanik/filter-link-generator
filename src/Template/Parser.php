<?php

namespace FilterLinkGenerator\Template;

use FilterLinkGenerator\Exception\LexerException;
use FilterLinkGenerator\Template\Instances\BlockPart;
use FilterLinkGenerator\Template\Instances\StaticPart;
use FilterLinkGenerator\Template\Lexer\Lexer;

class Parser
{
    const REGEXP_VAR = "/\{\$[\w\-+*\/]+\}/mi";
    const REGEXP_BLOCK = '/{[\w\/\-_=]*{\$[a-z\-_0-9]+}[\w\/\-_=]*}/mi';

    public array $template = [];

    public function parseTemplate(string $template): array
    {
        $lexer = new Lexer ($template);

        $this->template=$lexer->startParsing();

        return $this->template;
    }

    private function getBlocks(string $template)
    {
        preg_match_all(self::REGEXP_BLOCK, $template, $blocks, PREG_SET_ORDER, 0);
        return $blocks;
    }
}