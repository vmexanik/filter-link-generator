<?php

require_once '../../vendor/autoload.php';

use FilterLinkGenerator\FilterLinkGenerator;

$template = "/my-project/{my-filter={\$param}/}{b/{\$brand}}";

$data = [
    'data' => [
        'param' => [
            'first_param',
            'second_param',
            'third_param'
        ],
        'brand' => [
            'ate',
            'bosch',
            'abs'
        ]
    ],
    'selected' => [
        'param'=>[
            'second_param',
            'third_param'
        ],
        'brand' => [
            'ate',
            'bosch',
            'abs'
        ]
    ],
    'separator' => '_'
];

$filterLinks = new FilterLinkGenerator();
$filterLinks->setTemplate($template);
$filterLinks->setSeparator($data['separator']);
$filterLinks->setSelectedParams($data['selected']);
$filterLinks->setParams($data['data']);

$generatedLinks = $filterLinks->generateLink();

print_r($generatedLinks);
