<?php

namespace FilterLinkGenerator\Template\Instances;

interface TemplatePart
{
    public function __construct($data);

    public function getData();

    public function getVarName(): string;

    public function setData(array $data);

    public function setSelected(array $selectedParams);

    public function setSeparator(string $separator);

    public function getOnlySelectedData();
}
