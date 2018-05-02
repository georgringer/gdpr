<?php

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer::class] = array(
    'className' => \GeorgRinger\Gdpr\Xclass\DefaultRestrictionContainer::class
);
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer::class] = array(
    'className' => \GeorgRinger\Gdpr\Xclass\FrontendRestrictionContainer::class
);


$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Authentication\BackendUserAuthentication::class] = array(
    'className' => \GeorgRinger\Gdpr\Xclass\BackendUserAuthentication::class
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
    = \GeorgRinger\Gdpr\Hooks\DataHandlerHook::class;

if (TYPO3_MODE === 'BE') {
    $isVersion9Up = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 9000000;
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'GeorgRinger.gdpr',
        $isVersion9Up ? 'site' : 'tools',
        'tx_gdpr_m1',
        '',
        ['Administration' => 'index,help,search,delete,disable,reenable,randomize,moduleNotEnabled,log,configuration,formOverview,formStatusUpdate'],
        [
            'access' => 'user,group',
            'icon' => 'EXT:gdpr/Resources/Public/Icons/module.svg',
            'labels' => 'LLL:EXT:gdpr/Resources/Private/Language/locallang_modadministration.xlf',
        ]
    );


    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['system'][] = \GeorgRinger\Gdpr\Report\GdprStatusReport::class;

}
