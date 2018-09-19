<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Make TYPO3 compatible to GDPR',
    'description' => '',
    'category' => 'module',
    'author' => 'Georg Ringer',
    'author_email' => 'mail@ringer.it',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.14-9.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
