<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Make TYPO3 compatible to GDPR',
    'description' => 'This extensions enables you as site owner and extension developer to comply with the GDPR by covering some of the important aspects',
    'category' => 'module',
    'author' => 'Georg Ringer',
    'author_email' => 'mail@ringer.it',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
