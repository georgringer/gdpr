<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Domain\Repository;

use GeorgRinger\Gdpr\Domain\Model\Dto\LogFilter;
use TYPO3\CMS\Core\Utility\MathUtility;

class LogRepository extends BaseRepository
{

    const LOG_TABLE = 'tx_gdpr_domain_model_log';

    public function filter(LogFilter $filter): array
    {
        $queryBuilder = $this->getQueryBuilder(self::LOG_TABLE);

        $where = [];
        if (!empty($filter->getTableName())) {
            $where[] = $queryBuilder->expr()->like('table_name', $queryBuilder->createNamedParameter($filter->getTableName(), \PDO::PARAM_STR));
        }
        if ($filter->getStatus()) {
            $where[] = $queryBuilder->expr()->eq('status', $queryBuilder->createNamedParameter($filter->getStatus(), \PDO::PARAM_INT));
        }
        $dateFrom = $filter->getDateFrom();
        if ($dateFrom) {
            $date = $this->getTimeRestriction($dateFrom);
            if ($date) {
                $where[] = $queryBuilder->expr()->gte('tstamp', $queryBuilder->createNamedParameter($date, \PDO::PARAM_INT));
            }
        }
        $dateTo = $filter->getDateTo();
        if ($dateTo) {
            $date = $this->getTimeRestriction($dateTo);
            if ($date) {
                $where[] = $queryBuilder->expr()->lte('tstamp', $queryBuilder->createNamedParameter($date, \PDO::PARAM_INT));
            }
        }

        $res = $queryBuilder
            ->select('*')
            ->from(self::LOG_TABLE)
            ->setMaxResults($filter->getLimit())
            ->orderBy('tstamp', 'desc');

        if (!empty($where)) {
            $res->where(...$where);
        }

        return $res->execute()->fetchAll();
    }

    /**
     * @param string|int $timeInput
     * @return int
     */
    private function getTimeRestriction($timeInput): int
    {
        $timeLimit = 0;
        if (MathUtility::canBeInterpretedAsInteger($timeInput)) {
            $timeLimit = $GLOBALS['SIM_EXEC_TIME'] - $timeInput;
        } else {
            $timeByFormat = \DateTime::createFromFormat('HH:mm DD-MM-YYYY', $timeInput);
            if ($timeByFormat) {
                $timeLimit = $timeByFormat->getTimestamp();
            } else {
                // try to check strtotime
                $timeFromString = strtotime($timeInput);

                if ($timeFromString) {
                    $timeLimit = $timeFromString;
                }
            }
        }
        return $timeLimit;
    }

}
