<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Domain\Repository;

use GeorgRinger\Gdpr\Domain\Model\Dto\LogFilter;
use TYPO3\CMS\Core\Utility\MathUtility;

class LogRepository extends BaseRepository
{

    const LOG_TABLE = 'tx_gdpr_domain_model_log';



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
