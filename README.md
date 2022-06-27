# Filter link generator

Filter link generator is an easy-to-use library for generaring human-readable filter links. It can be use for generating links for filter in self-written CMS or frameworks that do not have these features

# Instalation

Use [composer](https://getcomposer.org/) to install FilterLinkGenerator into your project

```bash
composer require vmexanik/filterlinkgenerator
```

# Basic Usage

```php
<?php

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

$generatedLinks = $filterLinks->generateLink()

```

The $generatedLinks array will contain the generated links for your filter

```php
Array
(
    [param] => Array
        (
            [first_param] => /my-project/my-filter=second_param_first_param/
            [second_param] => /my-project/my-filter=/
            [third_param] => /my-project/my-filter=second_param_third_param/
        )

)
```
