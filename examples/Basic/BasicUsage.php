<?php

require_once '../../vendor/autoload.php';

use FilterLinkGenerator\FilterLinkGenerator;

$template = "/my-project/{[e]my-filter={\$param}}{[e]b/{\$brand}}/";

$data = [
    'vars' => [
        'param' => [
            'values' => [
                'first_param',
                'second_param',
                'third_param'
            ],
            'separator'=>'-'
        ],
        'brand' => [
            'values'=>[
                'ate',
                'bosch',
                'abs'
            ],
            'separator'=>'-'
        ]
    ],
    'selected' => [
        'param' => [
            'second_param',
            'third_param'
        ],
        'brand' => [
            'ate',
            'bosch',
            'abs'
        ]
    ],
    'block_separator' => '_'
];

$filterLinks = new FilterLinkGenerator($template,$data);
$generatedLinks = $filterLinks->generateLink();

print_r($generatedLinks);
