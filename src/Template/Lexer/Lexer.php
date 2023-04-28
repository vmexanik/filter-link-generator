<?php

declare(strict_types=1);


namespace FilterLinkGenerator\Template\Lexer;

use ErrorException;
use FilterLinkGenerator\Control\ControlBuilder;
use FilterLinkGenerator\Exception\LexerException;
use FilterLinkGenerator\Template\Instances\BlockPart;
use FilterLinkGenerator\Template\Instances\StaticPart;

class Lexer
{
    const REGEX_OPEN_TAG = "/(?<!\\\\)(\{)/mi";
    const REGEX_CLOSE_TAG = "/(?<!\\\\)\}/mi";
    const REGEX_CONTROL_CHAR = "/(?<=\{)\[(.+)\]/mUi";
    const REGEX_VAR = "/\{\\$[a-zA-Z_]+\}/mi";

    const ALLOWED_CONTROL_CHAR = ['e'];
    private string $template;
    private int $currentPosition = 0;
    private BlockPart $blockPart;
    private $templateLength;

    private $partBuffer = '';

    public function __construct(string $template)
    {
        $this->template = $template;
        $this->templateLength = mb_strlen($template);
    }

    public function detectOpenTag($char): bool
    {
        return $char=='{';
    }

    /**
     * @throws ErrorException
     */
    public function detectControlChar()
    {
        $searchResult = preg_match(self::REGEX_CONTROL_CHAR, $this->template, $matches, PREG_OFFSET_CAPTURE, $this->currentPosition);
        if ($searchResult > 0) {
            $controlChar = $matches[1][0];

            if (!empty($controlChar)) {

                $controlChar = explode(',', $controlChar);

                if (empty(array_diff($controlChar, self::ALLOWED_CONTROL_CHAR))) {
                    foreach ($controlChar as $char) {
                        $controlPath = ControlBuilder::CONTROLS[$char];
                        $this->blockPart->pushControl(new $controlPath);
                    }
                } else {
                    throw new ErrorException('Control char not valid.');
                }
            }

            $this->currentPosition = end($matches)[1] + 2;
        }
    }

    public function detectVar(): bool
    {
        $template = substr($this->template, $this->currentPosition);
        $searchResult = preg_match(self::REGEX_VAR, $this->template, $matches, PREG_OFFSET_CAPTURE, $this->currentPosition);
        if ($searchResult > 0) {
            $end = end($matches);
            $this->currentPosition = $end[1] + mb_strlen($end[0]);
            return true;
        }

        return false;
    }

    public function detectCloseTag($char): bool
    {
        return $char=='}';
    }

    public function isEndTemplate(): bool
    {
        return $this->currentPosition < $this->templateLength;
    }

    public function startParsing(): array
    {
        $chars = mb_str_split($this->template);

        $template = [];

        $block = '';
        $static = '';
        $tree = 0;
        $trigger=0;

        foreach ($chars as $char) {

            if ($this->detectOpenTag($char)) {
                if ($tree == 0)
                    $trigger = 1;
                $tree++;
            }

            if ($this->detectCloseTag($char)) {
                $tree--;

                if ($tree == 0) {
                    $block .= $char;
                    $trigger = 1;
                }
            }

            if ($tree == 0 && $trigger==0) {
                $static .= $char;
            }

            if ($tree > 0 ) {
                $block .= $char;
            }

            if ($trigger) {
                $trigger = 0;

                if (!empty($static)){
                    $template[]=new StaticPart($static);
                    $static='';
                }

                if (!empty($block) && $tree==0){
                    $template[]=new BlockPart($block);
                    $block='';
                }
            }
        }

        return $template;
    }

    public function getTemplatePart()
    {
        if (!empty($this->partBuffer)) {
            $partTemplate = $this->partBuffer;
            $this->partBuffer = '';
            return $partTemplate;
        } elseif ($this->currentPosition > 0) {
            $startBlock = $this->currentPosition;
            if ($this->detectOpenTag()) {
                $this->detectVar();
                $this->detectCloseTag();

                $part = mb_substr($this->template, $startBlock, $this->currentPosition - $startBlock);
                return new BlockPart(mb_substr($this->template, $startBlock, $this->currentPosition - $startBlock));
            } else {
                $part = mb_substr($this->template, $startBlock);
                $this->currentPosition += mb_strlen($part);
                return new StaticPart($part);
            }

        }
    }
}