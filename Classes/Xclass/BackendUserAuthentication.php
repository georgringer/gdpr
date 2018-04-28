<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Xclass;

use GeorgRinger\Gdpr\Service\IpAnonymizer;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserAuthentication extends \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
{

    /**
     * Writes an entry in the logfile/table
     * Documentation in "TYPO3 Core API"
     *
     * @param int $type Denotes which module that has submitted the entry. See "TYPO3 Core API". Use "4" for extensions.
     * @param int $action Denotes which specific operation that wrote the entry. Use "0" when no sub-categorizing applies
     * @param int $error Flag. 0 = message, 1 = error (user problem), 2 = System Error (which should not happen), 3 = security notice (admin)
     * @param int $details_nr The message number. Specific for each $type and $action. This will make it possible to translate errormessages to other languages
     * @param string $details Default text that follows the message (in english!). Possibly translated by identification through type/action/details_nr
     * @param array $data Data that follows the log. Might be used to carry special information. If an array the first 5 entries (0-4) will be sprintf'ed with the details-text
     * @param string $tablename Table name. Special field used by tce_main.php.
     * @param int|string $recuid Record UID. Special field used by tce_main.php.
     * @param int|string $recpid Record PID. Special field used by tce_main.php. OBSOLETE
     * @param int $event_pid The page_uid (pid) where the event occurred. Used to select log-content for specific pages.
     * @param string $NEWid Special field used by tce_main.php. NEWid string of newly created records.
     * @param int $userId Alternative Backend User ID (used for logging login actions where this is not yet known).
     * @return int Log entry ID.
     */
    public function writelog($type, $action, $error, $details_nr, $details, $data, $tablename = '', $recuid = '', $recpid = '', $event_pid = -1, $NEWid = '', $userId = 0)
    {
        if (!$userId && !empty($this->user['uid'])) {
            $userId = $this->user['uid'];
        }

        if (!empty($this->user['ses_backuserid'])) {
            if (empty($data)) {
                $data = [];
            }
            $data['originalUser'] = $this->user['ses_backuserid'];
        }

        $fields = [
            'userid' => (int)$userId,
            'type' => (int)$type,
            'action' => (int)$action,
            'error' => (int)$error,
            'details_nr' => (int)$details_nr,
            'details' => $details,
            'log_data' => serialize($data),
            'tablename' => $tablename,
            'recuid' => (int)$recuid,
            'IP' => IpAnonymizer::anonymizeIp((string)GeneralUtility::getIndpEnv('REMOTE_ADDR')),
            'tstamp' => $GLOBALS['EXEC_TIME'] ?? time(),
            'event_pid' => (int)$event_pid,
            'NEWid' => $NEWid,
            'workspace' => $this->workspace
        ];

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_log');
        $connection->insert(
            'sys_log',
            $fields,
            [
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
            ]
        );

        return (int)$connection->lastInsertId('sys_log');
    }
}