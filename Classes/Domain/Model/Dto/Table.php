<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Domain\Model\Dto;

use GeorgRinger\Gdpr\Service\TableInformation;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Table
{
    /** @var string */
    protected $tableName = '';

    /** @var string */
    protected $title = '';

    /** @var string */
    protected $titleField = '';

    /** @var string */
    protected $titleLabel = '';

    /** @var string */
    protected $gdprRestrictionField = '';

    /** @var string */
    protected $gdprRandomizedField = '';

    /** @var string */
    protected $gdprRandomizedDateField = '';

    /** @var int */
    protected $gdprExpirePeriod = 0;

    /** @var array */
    protected $gdprRandomizeMapping = [];

    public function __construct(string $tableName)
    {
        if (!TableInformation::isTableEnabled($tableName)) {
            throw new \UnexpectedValueException(sprintf('Table "%s" is not enabled for GDPR', $tableName), 1519298518);
        }

        $tcaCtrl = $GLOBALS['TCA'][$tableName]['ctrl'];

        $this->tableName = $tableName;
        $this->title = $tcaCtrl['title'];
        $this->titleField = $tcaCtrl['label'];
        $this->titleLabel = $GLOBALS['TCA'][$tableName]['columns'][$this->titleField]['label'];
        $this->gdprRestrictionField = $tcaCtrl['gdpr']['restriction_field'] ?: '';
        $this->gdprRandomizedField = $tcaCtrl['gdpr']['randomized_field'] ?: '';
        $this->gdprRandomizeMapping = $tcaCtrl['gdpr']['randomize_mapping'] ?: [];
        $this->gdprRandomizedDateField = $tcaCtrl['gdpr']['randomize_datefield'] ?: '';
        $this->gdprExpirePeriod = $tcaCtrl['gdpr']['randomize_expirePeriod'] ?: 365;
    }

    /**
     * @param string $tableName
     * @return Table
     */
    public static function getInstance(string $tableName): self
    {
        return GeneralUtility::makeInstance(__CLASS__, $tableName);
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getTitleField(): string
    {
        return $this->titleField;
    }

    /**
     * @return string
     */
    public function getTitleLabel(): string
    {
        return $this->titleLabel;
    }

    /**
     * @return string
     */
    public function getGdprRestrictionField(): string
    {
        return $this->gdprRestrictionField;
    }

    /**
     * @return string
     */
    public function getGdprRandomizedField(): string
    {
        return $this->gdprRandomizedField;
    }

    /**
     * @return array
     */
    public function getGdprRandomizeMapping(): array
    {
        return $this->gdprRandomizeMapping;
    }

    /**
     * @return string
     */
    public function getGdprRandomizedDateField(): string
    {
        return $this->gdprRandomizedDateField;
    }

    /**
     * @return int
     */
    public function getGdprExpirePeriod(): int
    {
        return $this->gdprExpirePeriod;
    }

    public function randomizationEnabled(): bool
    {
        return !empty($this->gdprRandomizedField) && !empty($this->gdprRandomizeMapping);
    }

}
