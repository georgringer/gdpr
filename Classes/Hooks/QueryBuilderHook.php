<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Hooks;

use GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction;
use GeorgRinger\Gdpr\Domain\Model\Dto\Table;
use GeorgRinger\Gdpr\Log\LogManager;
use GeorgRinger\Gdpr\Service\TableInformation;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class QueryBuilderHook
{

    public function addAdditionalWhereConditions(QueryBuilder $queryBuilder) {
        $fo = $queryBuilder->getRestrictionContainer();
        $fo->add(GeneralUtility::makeInstance(GdprRestriction::class));
    }
}