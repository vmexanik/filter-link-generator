<?php

/**
 * Filter links generator main class
 *
 * @link https://github.com/vmexanik/filter-link-generator
 *
 */

declare(strict_types = 1);

namespace FilterLinkGenerator;

class FilterLinkGenerator
{

    /**
     * @var string
     */
    private string $template;

    /**
     * @var array
     */
    private array $data;

    /**
     * @var array
     */
    private $vars;

    /**
     * @param string $template template for generating link
     * @param array $data input data for generating link
     */
    public function __construct(string $template, array $data)
    {
        preg_match_all('/(?<=\{)\w+(?=})/m', $template, $vars);
        $this->checkData($vars,$data);

        $this->template=$template;
        $this->data=$data;
        $this->vars=$vars;
    }

    final public function generateLink(): array
    {
        $returnLinks=[];
        foreach ($this->vars[0] as $var){
            $returnLinks[$var]=$this->addVar($this->template,$var,$this->data[$var]);
        }

        return $returnLinks;
    }

    private function addVar($template, $var, $data): array
    {
        $resultArray=[];

        if (!empty($data['selected']))
            $this->modifyTemplate($template,$var,$data);

        foreach ($data['data'] as $varValue){
            if (in_array($varValue,$data['selected'])) {
                $search=[
                    $varValue.$data['separator'],
                    $data['separator']."{".$var."}",
                    "{".$var."}",
                    $data['separator'].$varValue];
                $varValueForReplace = '';
            }else{
                $search="{".$var."}";
                $varValueForReplace = $varValue;
            }

            $resultArray[$varValue]= str_replace([$data['separator'].$data['separator'],'//'],'',str_replace($search,$varValueForReplace, $template));
        }

        return $resultArray;
    }

    /**
     * @param $vars
     * @param $data
     * @return void
     *
     * input array
     * 'slug'=>[
     *      'data'=>[
     *          array of values like
     *          abs,
     *          abe,
     *          brembo....
     *      ],
     *      'selected'=>[
     *          array of selected values like
     *          abs,
     *          abe
     *      ],
     *      'delimiter'=>''
     * ]
     */

    private function checkData($vars, $data)
    {
        if (empty($vars[0])){
            throw new \InvalidArgumentException("Template must have minimum one var");
        }

        foreach ($vars[0] as $var){
            if (!is_array($data[$var]) || !is_array($data[$var]['data']))
                throw new \InvalidArgumentException('data to replace variable {'.$var.'} must be an array');

            if (empty($data[$var]) || empty($data[$var]['data']))
                throw new \InvalidArgumentException('variable {'.$var.'} has no data to replace');
        }
    }

    private function modifyTemplate(&$template, $var, $data): void
    {
        $allSeletedItemsOnSlug=implode($data['separator'],$data['selected']);
        $replacedTemplate=str_replace('{'.$var.'}',$allSeletedItemsOnSlug.$data['separator'].'{'.$var.'}', $template);
        $template=$replacedTemplate;
    }
}