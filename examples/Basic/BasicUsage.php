<?php

require_once '../../src/FilterLinkGenerator.php';

use FilterLinkGenerator\FilterLinkGenerator;

$template = "/my-project/my-filter={param}/";

$data = [
    'param' => [
        'data' => [
            'first_param',
            'second_param',
            'third_param'
        ],
        'selected' => [
            'second_param'
        ],
        'separator' => '_'
    ]
];

$filterLinks = new FilterLinkGenerator($template, $data);

$generatedLinks = $filterLinks->generateLink();

print_r($generatedLinks);
