<?php

$GLOBALS['TYPO3_CONF_VARS']['DB']['QueryRestrictions'][\TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer::class]['defaultRestrictionTypes'][] = \GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction::class;
$GLOBALS['TYPO3_CONF_VARS']['DB']['QueryRestrictions'][\TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer::class]['forcedRestrictionTypes'][] = \GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction::class;

$GLOBALS['TYPO3_CONF_VARS']['DB']['QueryRestrictions'][\TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer::class]['defaultRestrictionTypes'][] = \GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction::class;
$GLOBALS['TYPO3_CONF_VARS']['DB']['QueryRestrictions'][\TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer::class]['forcedRestrictionTypes'][] = \GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction::class;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Database\Query\QueryBuilder::class]['addAdditionalWhereConditions'][1521636341]
    = \GeorgRinger\Gdpr\Hooks\QueryBuilderHook::class;


$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$icons = [
    'ext-gdpr-form-overview' => 'form-overview.svg',
];
foreach ($icons as $identifier => $path) {
    $iconRegistry->registerIcon(
        $identifier,
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:gdpr/Resources/Public/Icons/' . $path]
    );
}