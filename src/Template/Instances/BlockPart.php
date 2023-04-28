<?php

namespace FilterLinkGenerator\Template\Instances;

class BlockPart implements TemplatePart
{
    const REGEX_VAR = "/\{\\$[a-zA-Z_]+\}/mi";
    const REGEX_VAR_WITHOUT_BLOCK = "/^\{\\$[a-zA-Z_]+\}$/mi";

    const REGEX_CONTROL_CHAR = "/^\{(\[.+\])/miU";
    private array $data;
    private string $separator;
    private array $selectedParams;

    private array $controls=[];
    /**
     * @var string[][]
     */
    private array $vars;

    public function __construct($block)
    {
        if ($this->varWithoutBlock($block)){
            $block=$this->addEmptyBlock($block);
        }

        $this->parse($block);

        $this->data = [
            'data' => preg_replace(['/^\{\[.+\]/m', '/\}$/m'],'',$block),
            'var' => $this->vars
        ];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        sort($this->data['varValues']);

        $varName = $this->getVarName();
        $selectedPart=[];

        foreach ($this->data['varValues'] as $value){
            if (in_array($value, $this->selectedParams)) {
                $selectedParts = array_diff($this->selectedParams, [$value]);
                $values=$selectedParts;
            } else {
                $selectedParts=$this->selectedParams;
                $values=array_merge($selectedParts,[$value]);
            }

            sort($values);

            if ($values)
                $selectedPart[$value]= str_replace("{\$$varName}",implode($this->separator,$values), $this->data['data']);
            else
                $selectedPart[$value]='';
        }

        return $selectedPart;
    }

    public function pushControl($param)
    {
        $this->controls[]=$param;
    }

    private function parse($block)
    {
        preg_match_all(self::REGEX_VAR, $block, $vars);
        preg_match(self::REGEX_CONTROL_CHAR, $block, $controlChars);

        $this->controls=explode(',',$controlChars[1]);
        $this->vars=$vars;
    }

    public function getVarName(): string
    {
        return str_replace(['{$', '}'], '', $this->data['var'][0])[0];
    }

    public function setData(array $data)
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
            return str_replace("{\$$varName}", $selectedValues, $this->data['data']);
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

    private function varWithoutBlock($block): bool
    {
        if (preg_match(self::REGEX_VAR_WITHOUT_BLOCK, $block)){
            return true;
        }

        return false;
    }

    private function addEmptyBlock($block): string
    {
        return "{$block}";
    }
}