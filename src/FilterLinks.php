<?php

namespace FilterLinks;

class FilterLinks
{
/*
 *      /search/{search_query}/{brand}/
 *      /search/brake/abs
 *      /search/brake/abe
 *      /search/brake/brembo
 *
 *      if selected
 *
 *      /search/brake/abs_abe
 *      /search/brake/abs_brembo
 *
 *
 * */

    public function generateLink($template, $data): array
    {
        preg_match_all('/(?<=\{)\w+(?=})/m', $template, $slugs);
        $this->checkData($slugs,$data);

        $returnLinks=[];
        foreach ($slugs[0] as $slug){
            $returnLinks[$slug]=$this->addSlug($template,$slug,$data[$slug]);
        }

        return $returnLinks;
    }

    private function addSlug($template, $slug, $data): array
    {
        $resultArray=[];

        if (!empty($data['selected']))
            $this->modifyTemplate($template,$slug,$data);

        foreach ($data['data'] as $slugValue){
            if (in_array($slugValue,$data['selected'])) {
                $search=[
                    $slugValue.$data['separator'],
                    $data['separator']."{".$slug."}",
                    "{".$slug."}",
                    $data['separator'].$slugValue];
                $slugValueForReplace = '';
            }else{
                $search="{".$slug."}";
                $slugValueForReplace = $slugValue;
            }

            $resultArray[$slugValue]= str_replace([$data['separator'].$data['separator'],'//'],'',str_replace($search,$slugValueForReplace, $template));
        }

        return $resultArray;
    }

    /**
     * @param $slugs
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

    private function checkData($slugs, $data)
    {
        if (empty($slugs[0])){
            throw new \InvalidArgumentException("Template must have minimum one slug");
        }

        foreach ($slugs[0] as $slug){
            if (!isset($data[$slug]) || !isset($data[$slug]['data']))
                throw new \InvalidArgumentException("$slug slug not have input array");

            if (empty($data[$slug]) || empty($data[$slug]['data']))
                throw new \InvalidArgumentException("$slug slug have empty input array");
        }
    }

    private function modifyTemplate(&$template, $slug, $data): void
    {
        $allSeletedItemsOnSlug=implode($data['separator'],$data['selected']);
        $replacedTemplate=str_replace('{'.$slug.'}',$allSeletedItemsOnSlug.$data['separator'].'{'.$slug.'}', $template);
        $template=$replacedTemplate;
    }
}