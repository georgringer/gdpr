<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Domain\Model\Dto;

use TYPO3\CMS\Core\Utility\MathUtility;

class LogFilter
{

    const DEFAULT_LIMIT = 50;
    const MAX_LIMIT = 200;

    /** @var string */
    protected $tableName = '';

    /** @var int */
    protected $status = 0;

    /** @var string */
    protected $dateFrom = '';

    /** @var string */
    protected $dateTo = '';

    /** @var int */
    protected $limit = self::DEFAULT_LIMIT;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status = 0)
    {
        $this->status = (int)$status;
    }

    /**
     * @return string
     */
    public function getDateFrom(): string
    {
        return $this->dateFrom;
    }

    /**
     * @param string $dateFrom
     */
    public function setDateFrom(string $dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return string
     */
    public function getDateTo(): string
    {
        return $this->dateTo;
    }

    /**
     * @param string $dateTo
     */
    public function setDateTo(string $dateTo)
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return MathUtility::forceIntegerInRange($this->limit, 10, self::MAX_LIMIT, self::DEFAULT_LIMIT);
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = (int)$limit;
    }

}
