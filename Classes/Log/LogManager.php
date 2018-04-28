<?php

namespace GeorgRinger\Gdpr\Log;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class LogManager implements SingletonInterface
{

    const STATUS_DELETE = 1;
    const STATUS_RANDOMIZE = 2;
    const STATUS_REENABLE = 3;
    const STATUS_RESTRICT = 4;
    const STATUS_IP_ANONYMIZE = 5;

    /**
     * @param string $tableName name of record's table
     * @param string|int $id id of record
     * @param int $status status type
     */
    public function log(string $tableName, $id, int $status)
    {
        if (is_string($id) && StringUtility::beginsWith($id, 'NEW')) {
            return;
        }
        if ($status < 1 || $status > 5) {
            throw new \UnexpectedValueException(sprintf('Value "%s" is invalid, use one of [1,2,3,4]', $status));
        }
        $backendUser = $this->getBackendUser()->user;
        $fieldValues = [
            'tstamp' => $GLOBALS['EXEC_TIME'],
            'table_name' => $tableName,
            'record_id' => $id,
            'status' => $status,
        ];
        if (is_array($backendUser)) {
            $fieldValues['user'] = $backendUser['uid'];
            $fieldValues['user_name_text'] = $this->getUsernameText($backendUser);
        } else {
            // @todo CLI?
            $fieldValues['user'] = -1;
            $fieldValues['user_name_text'] = 'CLI';
        }


        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_gdpr_domain_model_log')
            ->insert('tx_gdpr_domain_model_log', $fieldValues);
    }

    protected function getUsernameText(array $user): string
    {
        $data = [
            'username' => $user['username'],
            'realName' => $user['realName'],
            'email' => $user['email']
        ];
        return json_encode($data);
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}