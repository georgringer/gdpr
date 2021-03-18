<?php

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
    = \GeorgRinger\Gdpr\Hooks\DataHandlerHook::class;

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Gdpr',
        'site',
        'tx_gdpr_m1',
        '',
        [\GeorgRinger\Gdpr\Controller\AdministrationController::class => 'index,help,search,delete,disable,reenable,randomize,moduleNotEnabled,log,configuration,formOverview,formStatusUpdate'],
        [
            'access' => 'user,group',
            'icon' => 'EXT:gdpr/Resources/Public/Icons/module.svg',
            'labels' => 'LLL:EXT:gdpr/Resources/Private/Language/locallang_modadministration.xlf',
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['system'][] = \GeorgRinger\Gdpr\Report\GdprStatusReport::class;
}
