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


    public function enableRecord(string $table, int $id)
    {
        $this->switchGdprRestriction($table, $id, 0);
        $this->logger->log($table, $id, LogManager::STATUS_REENABLE);
    }

    public function disableRecord(string $table, int $id)
    {
        $this->switchGdprRestriction($table, $id, 1);
        $this->logger->log($table, $id, LogManager::STATUS_RESTRICT);
    }

    public function deleteRecord(string $table, int $id)
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], []);
        $dataHandler->disableDeleteClause();
        $dataHandler->deleteEl($table, $id, true, true);
        $this->logger->log($table, $id, LogManager::STATUS_DELETE);
    }

    public function randomizeRecord(string $table, int $id)
    {
        $tableInformation = Table::getInstance($table);

        $randomizationService = GeneralUtility::makeInstance(Randomization::class, $table);
        $newValues = $randomizationService->generateDataForTable();

        $newValues[$tableInformation->getGdprRestrictionField()] = 0;
        $newValues[$tableInformation->getGdprRandomizedField()] = 1;

        $this->getConnection($table)->update(
            $table,
            $newValues,
            [
                'uid' => $id
            ]
        );
        $this->logger->log($table, $id, LogManager::STATUS_RANDOMIZE);
    }

    public function search(Search $search): array
    {
        $swords = $search->getSearchWord();
        $out = [];
        if (!$swords) {
            return [];
        }

        foreach (TableInformation::getAllEnabledTables() as $table) {
            // Get fields list
            $conf = $GLOBALS['TCA'][$table];
            // Avoid querying tables with no columns
            if (empty($conf['columns'])) {
                continue;
            }
            $connection = $this->getConnection($table);
            $tableColumns = $connection->getSchemaManager()->listTableColumns($table);
            $fieldsInDatabase = [];
            foreach ($tableColumns as $column) {
                $fieldsInDatabase[] = $column->getName();
            }
            $fields = array_intersect(array_keys($conf['columns']), $fieldsInDatabase);

            $queryBuilder = $connection->createQueryBuilder();
            $queryBuilder->getRestrictions()
                ->removeAll()
                ->removeByType(GdprRestriction::class);
            if ($search->isSensitiveOnly()) {
                $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(GdprOnlyRestriction::class));
            }
            $queryBuilder->count('*')->from($table);
            $likes = [];
            $excapedLikeString = '%' . $queryBuilder->escapeLikeWildcards($swords) . '%';
            foreach ($fields as $field) {
                $likes[] = $queryBuilder->expr()->like(
                    $field,
                    $queryBuilder->createNamedParameter($excapedLikeString, \PDO::PARAM_STR)
                );
            }
            $count = $queryBuilder->orWhere(...$likes)->execute()->fetchColumn(0);

            if ($count > 0) {
                $queryBuilder = $connection->createQueryBuilder();
                $queryBuilder->getRestrictions()
                    ->removeAll()
                    ->removeByType(GdprRestriction::class);
                if ($search->isSensitiveOnly()) {
                    $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(GdprOnlyRestriction::class));
                }

                $tableInformation = Table::getInstance($table);
                $restrictionFieldName = $tableInformation->getGdprRestrictionField();

                $queryBuilder->select('uid', $conf['ctrl']['label'], $restrictionFieldName)
                    ->from($table)
                    ->setMaxResults(200);
                $likes = [];
                foreach ($fields as $field) {
                    $likes[] = $queryBuilder->expr()->like(
                        $field,
                        $queryBuilder->createNamedParameter($excapedLikeString, \PDO::PARAM_STR)
                    );
                }
                $statement = $queryBuilder->orWhere(...$likes)->execute();
                $lastRow = null;
                while ($row = $statement->fetch()) {
                    $out[$table]['rows'][] = $row;
                }
                $out[$table]['meta'] = Table::getInstance($table);
            }
        }
        return $out;
    }

    private function switchGdprRestriction(string $table, int $id, int $value)
    {
        $this->getConnection($table)->update(
            $table,
            [
                Table::getInstance($table)->getGdprRestrictionField() => $value
            ],
            [
                'uid' => $id
            ]
        );
    }


}
