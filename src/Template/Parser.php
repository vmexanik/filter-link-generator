<?php

namespace FilterLinkGenerator\Template;

use FilterLinkGenerator\Template\Instances\BlockPart;
use FilterLinkGenerator\Template\Instances\StaticPart;

class Parser
{
    const REGEXP_VAR="/\{\$[\w\-+*\/]+\}/mi";
    const REGEXP_BLOCK="/\{[\w\/\-=]*[\w\{\}\$\-=]+[\w\/\-=]*\}/m";

    public array $template=[];

    public function parseTemplate(string $template): array
    {
        $blocks=$this->getBlocks($template);

        foreach ($blocks as $block){
            $len=mb_strlen($template);
            $staticPart=mb_strstr($template,$block[0],true);
            $staticPartLen=mb_strlen($staticPart);
            if ($staticPartLen)
                $this->template[]=new StaticPart($staticPart);
            $this->template[]=new BlockPart($block[0]);
            $blockLen=mb_strlen($block[0]);

            $template=mb_substr($template,$staticPartLen+$blockLen,$len);
        }

        return $this->template;
    }

    private function getBlocks(string $template)
    {
        preg_match_all(self::REGEXP_BLOCK,$template,$blocks,PREG_SET_ORDER, 0);
        return $blocks;
    }
}