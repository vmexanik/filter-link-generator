<?php

namespace FilterLinkGenerator\Template\Instances;

interface TemplatePart
{
    public function __construct($data);

    public function getData(): string;

    public function getVarName(): string;

    public function setData(string $data);

    public function setSelected(array $selectedParams);

    public function setSeparator(string $separator);

    public function getOnlySelectedData();
}
