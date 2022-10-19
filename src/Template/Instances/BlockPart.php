<?php

namespace FilterLinkGenerator\Template\Instances;

class BlockPart implements TemplatePart
{
    private array $data;
    private string $separator;
    private array $selectedParams;

    public function __construct($block)
    {
        $var = $this->parseVar($block);
        $this->data = [
            'data' => $block,
            'var' => $var
        ];
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        if (in_array($this->data['varValues'], $this->selectedParams)) {
            $selectedParts = array_diff($this->selectedParams, [$this->data['varValues']]);
            $selectedPart = $this->getSelectedPartDataForOnlyStaticPart($selectedParts, $this->separator);
            $this->data['varValues'] = '';
        } else {
            $selectedPart = $this->getSelectedPartData($this->selectedParams, $this->separator);
        }

        $varName = $this->getVarName();

        if (mb_strlen($selectedPart . $this->data['varValues'])>0) {
            $generatedBlock = str_replace("{\$$varName}", $selectedPart . $this->data['varValues'], $this->data['data']);
            return mb_substr($generatedBlock, 1, mb_strlen($generatedBlock) - 2);
        }else{
            return '';
        }
    }

    private function parseVar($block)
    {
        $re = '/\{\$[\w\-+*\/]+\}/mi';
        preg_match($re, $block, $vars);

        return $vars;
    }

    public function getVarName(): string
    {
        return str_replace(['{$', '}'], '', $this->data['var'])[0];
    }

    public function setData(string $data)
    {
        $this->data['varValues'] = $data;
    }

    public function getSelectedPartData($data, $separator): string
    {
        if (!empty($data))
            return implode($separator, $data) . $this->separator;
        else
            return '';
    }

    public function setSelected($selectedParams)
    {
        $this->selectedParams = $selectedParams;
    }

    public function getOnlySelectedData(): string
    {
        if (empty($this->selectedParams)) {
            return '';
        } else {
            $varName = $this->getVarName();
            $selectedValues = $this->getSelectedPartDataForOnlyStaticPart($this->selectedParams, $this->separator);
            $generatedBlock = str_replace("{\$$varName}", $selectedValues, $this->data['data']);
            return mb_substr($generatedBlock, 1, mb_strlen($generatedBlock) - 2);
        }
    }

    public function setSeparator($separator)
    {
        $this->separator = $separator;
    }

    public function getSelectedPartDataForOnlyStaticPart($data, $separator): string
    {
        if (!empty($data))
            return implode($separator, $data);
        else
            return '';
    }
}