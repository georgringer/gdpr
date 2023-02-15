<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Make TYPO3 compatible to GDPR',
    'description' => '',
    'category' => 'module',
    'author' => 'Georg Ringer',
    'author_email' => 'mail@ringer.it',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '1.2.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.1.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
