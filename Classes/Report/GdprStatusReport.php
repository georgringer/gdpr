<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Report;

use GeorgRinger\Gdpr\Domain\Repository\RecordRepository;
use GeorgRinger\Gdpr\Service\TableInformation;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Reports\Status as ReportStatus;
use TYPO3\CMS\Reports\StatusProviderInterface;

/**
 * Report for GDPR
 */
class GdprStatusReport implements StatusProviderInterface
{

    /**
     * Get status information
     *
     * @return array
     */
    public function getStatus(): array
    {
        $statuses = [
            'gdpr' => $this->getStatusOfGdpr(),
        ];
        return $statuses;
    }

    protected function getStatusOfGdpr(): ReportStatus
    {
        $recordRepository = GeneralUtility::makeInstance(RecordRepository::class);

        $messages = [];
        $actionRequired = false;
        $status = ReportStatus::OK;
        foreach (TableInformation::getAllEnabledTables() as $table) {
            $statistic = $recordRepository->getStatisticOfTable($table);
            $countAction = (int)$statistic[1]['count'];
            $countNoAction = (int)$statistic[0]['count'];
            $sum = $countAction + $countNoAction;
            if ($countAction > 0) {
                $status = ReportStatus::WARNING;
                $messages[] = sprintf('In Table "%s" are %s rows total, %s need an action!', $table, $sum, $countAction);
            } else {
                $messages[] = sprintf('In Table "%s" are %s rows total, no action required.', $table, $sum);
            }
        }

        $message = implode('<br>', $messages);


        return GeneralUtility::makeInstance(
            ReportStatus::class,
            'GDPR Handling',
            'Some information regarding GDPR related sensible information:',
            $message,
            $status
        );
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
