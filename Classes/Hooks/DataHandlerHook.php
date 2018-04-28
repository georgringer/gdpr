<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Hooks;

use GeorgRinger\Gdpr\Domain\Model\Dto\Table;
use GeorgRinger\Gdpr\Log\LogManager;
use GeorgRinger\Gdpr\Service\TableInformation;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandlerHook
{

    /** @var LogManager */
    protected $logManager;

    public function __construct()
    {
        $this->logManager = GeneralUtility::makeInstance(LogManager::class);
    }

    /**
     * Hooks into TCE Main and watches all record creations and updates. If it
     * detects that the new/updated record belongs to a table configured for
     * indexing through Solr, we add the record to the index queue.
     *
     * @param string $status Status of the current operation, 'new' or 'update'
     * @param string $table The table the record belongs to
     * @param mixed $uid The record's uid, [integer] or [string] (like 'NEW...')
     * @param array $fields The record's data
     * @param DataHandler $tceMain TYPO3 Core Engine parent object
     * @return void
     */
    public function processDatamap_afterDatabaseOperations(
        $status,
        $table,
        $uid,
        array $fields,
        DataHandler $tceMain
    )
    {
        if (TableInformation::isTableEnabled($table)) {
            $tableInformation = Table::getInstance($table);

            $restrictionField = $tableInformation->getGdprRestrictionField();
            if ($restrictionField && isset($fields[$restrictionField])) {
                if ($fields[$restrictionField]) {
                    $this->logManager->log($table, $uid, LogManager::STATUS_RESTRICT);
                } else {
                    $this->logManager->log($table, $uid, LogManager::STATUS_REENABLE);
                }
            }
        }
    }
}
