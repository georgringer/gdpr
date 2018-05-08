<?php
defined('TYPO3_MODE') or die();


$fields = [
    'gdpr_module_enable' => [
        'label' => 'LLL:EXT:gdpr/Resources/Private/Language/locallang.xlf:be_users.gdpr_module_enable',
        'config' => [
            'type' => 'check',
            'default' => 0
        ]
    ]
];

if (version_compare(TYPO3_branch, '9.2', '>=')) {
    $fields['gdpr_module_enable']['config'] = [
        'type' => 'check',
        'renderType' => 'checkboxLabeledToggle',
        'items' => [
            [
                0 => '',
                1 => '',
                'labelChecked' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.enabled',
                'labelUnchecked' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disabled'
            ],
        ],
    ];
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('be_users', $fields);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'be_users',
    'gdpr_module_enable',
    '',
    'before:disableIPlock'
);

$tca = \GeorgRinger\Gdpr\Service\Tca::getInstance('be_users');
$tca
    ->addRestriction('gdpr_restricted')
    ->add('after:disable');