<?php

$GLOBALS['TYPO3_CONF_VARS']['DB']['QueryRestrictions'][\TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer::class]['defaultRestrictionTypes'][] = \GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction::class;
$GLOBALS['TYPO3_CONF_VARS']['DB']['QueryRestrictions'][\TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer::class]['forcedRestrictionTypes'][] = \GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction::class;

$GLOBALS['TYPO3_CONF_VARS']['DB']['QueryRestrictions'][\TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer::class]['defaultRestrictionTypes'][] = \GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction::class;
$GLOBALS['TYPO3_CONF_VARS']['DB']['QueryRestrictions'][\TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer::class]['forcedRestrictionTypes'][] = \GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction::class;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Database\Query\QueryBuilder::class]['addAdditionalWhereConditions'][1521636341]
    = \GeorgRinger\Gdpr\Hooks\QueryBuilderHook::class;

$extConfiguration = \GeorgRinger\Gdpr\Domain\Model\Dto\ExtensionConfiguration::getInstance();
if ($extConfiguration->getOverloadMediaRenderer()) {
    $rendererRegistry = \TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::getInstance();
    $rendererRegistry->registerRendererClass(\GeorgRinger\Gdpr\Rendering\YoutubeRenderer::class);
}