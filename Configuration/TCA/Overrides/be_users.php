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