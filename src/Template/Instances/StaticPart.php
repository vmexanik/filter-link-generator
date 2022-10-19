<?php

namespace FilterLinkGenerator\Template\Instances;

class StaticPart implements TemplatePart
{
    private string $data;

    public function __construct($staticText)
    {
        $this->data=$staticText;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getVarName(): string
    {
        return '';
    }

    public function setData(string $data)
    {

    }

    public function setSelected($selectedParams)
    {

    }

    public function setSeparator($separator)
    {

    }

    public function getOnlySelectedData(): string
    {
        return $this->getData();
    }
}