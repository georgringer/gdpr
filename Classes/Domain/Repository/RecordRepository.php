<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Domain\Repository;

use GeorgRinger\Gdpr\Database\Query\Restriction\GdprOnlyRestriction;
use GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction;
use GeorgRinger\Gdpr\Domain\Model\Dto\Search;
use GeorgRinger\Gdpr\Domain\Model\Dto\Table;
use GeorgRinger\Gdpr\Log\LogManager;
use GeorgRinger\Gdpr\Service\Randomization;
use GeorgRinger\Gdpr\Service\TableInformation;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RecordRepository extends BaseRepository
{

    /** @var LogManager */
    protected $logger;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class);
    }

    public function getRestrictedRows(string $table): array
    {
        $tableInformation = Table::getInstance($table);

        $queryBuilder = $this->getQueryBuilder($table);
        $queryBuilder->getRestrictions()
            ->removeByType(GdprRestriction::class)
            ->removeByType(HiddenRestriction::class)
            ->add(GeneralUtility::makeInstance(GdprOnlyRestriction::class));

        return $queryBuilder
            ->select('uid', $tableInformation->getTitleField(), $tableInformation->getGdprRestrictionField())
            ->from($table)
            ->execute()
            ->fetchAll();
    }

    public function getStatisticOfTable(string $table): array
    {
        $restrictionFieldName = Table::getInstance($table)->getGdprRestrictionField();

        $queryBuilder = $this->getQueryBuilder($table);
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->removeByType(GdprRestriction::class);

        $rows = $queryBuilder
            ->select($restrictionFieldName)
            ->selectLiteral('count(' . $restrictionFieldName . ') as count')
            ->from($table)
            ->groupBy($restrictionFieldName)
            ->execute()
            ->fetchAll();

        return [
            'restricted' => (int)$rows[1]['count'],
            'public' => (int)$rows[0]['count']
        ];
    }




}
