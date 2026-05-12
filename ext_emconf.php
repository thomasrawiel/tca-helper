<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TCA Helper',
    'description' => 'TCA Helper functions: register CTypes, Doktypes & more',
    'state' => 'stable',
    'category' => 'misc',
    'author' => 'Thomas Rawiel',
    'author_email' => 'thomas.rawiel@gmail.com',
    'version' => '1.1.6',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
