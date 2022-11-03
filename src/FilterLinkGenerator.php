<?php

/**
 * Filter links generator main class
 *
 * @link https://github.com/vmexanik/filter-link-generator
 *
 */

declare(strict_types=1);

namespace FilterLinkGenerator;

use FilterLinkGenerator\Template\Instances\BlockPart;
use FilterLinkGenerator\Template\Instances\StaticPart;
use FilterLinkGenerator\Template\Parser;

class FilterLinkGenerator
{

    /**
     * @var string
     */
    private string $template;

    /**
     * @var array
     */
    private $vars;
    private $separator;
    private $params;
    private $selectedParams;
    private array $parsedTemplate;

    /**
     * @param string $template template for generating link
     * @param array $data input data for generating link
     */
    public function __construct(string $template = '', array $data = [])
    {
        if (!empty($template))
            $this->setTemplate($template);

        if (!empty($data['separator']))
            $this->setSeparator($data['separator']);

        if (!empty($data['selected']))
            $this->setSelectedParams($data['selected']);

        if (!empty($data['params']))
            $this->setParams($data['params']);
    }

    final public function generateLink(): array
    {
        $generatedLink=[];
        $parser = new Parser();
        $this->parsedTemplate = $parser->parseTemplate($this->template);

        $varList=$this->getVarList($this->parsedTemplate);

        foreach ($this->parsedTemplate as $instance) {
            $instance->setSelected($this->selectedParams[$instance->getVarName()]?:[]);
            $instance->setSeparator($this->separator);
        }

        foreach ($varList as $var){
            foreach ($this->params[$var] as $varValue) {
                $generatedLinks = '';

                foreach ($this->parsedTemplate as $instance) {
                    $instance->setData($varValue);

                    if ($var==$instance->getVarName()){
                        $generatedLinks .= $instance->getData();
                    }else{
                        $generatedLinks .= $instance->getOnlySelectedData();
                    }
                }
                $generatedLink[$var][$varValue]=$generatedLinks;
            }
        }

        return $generatedLink;
    }

    private function generatePartFromTemplate($instance): string
    {
        return $instance->getData();
    }

    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    public function setSeparator(string $separator)
    {
        $this->separator = $separator;
    }

    public function setSelectedParams(array $selectedParams)
    {
        $this->selectedParams = $selectedParams;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    private function getVarList(array $parsedTemplate): array
    {
        $varList=[];

        foreach ($parsedTemplate as $item){
            $varList[]=$item->getVarName();
        }

        return $varList;
    }

}