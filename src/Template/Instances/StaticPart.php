<?php

namespace FilterLinkGenerator\Template\Instances;

class StaticPart implements TemplatePart
{
    private string $data;

    public function __construct($staticText)
    {
        $this->data=$staticText;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getVarName(): string
    {
        return '';
    }

    public function setData(array $data)
    {

    }

    public function setSelected($selectedParams)
    {

    }

    public function setSeparator($separator)
    {

    }

    public function getOnlySelectedData()
    {
        return $this->getData();
    }
}