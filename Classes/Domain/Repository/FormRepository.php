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
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FormRepository extends BaseRepository
{
    /** @var UriBuilder */
    protected $uriBuilder;

    /** @var Registry */
    protected $registry;

    const REGISTRY_NAMESPACE = 'gdpr_form';

    public function __construct()
    {
        $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $this->registry = GeneralUtility::makeInstance(Registry::class);
    }

    public function getAllForms(): array
    {
        $data = [];

        if (ExtensionManagementUtility::isLoaded('foxrm')) {
            $data['ext-form'] = $this->getFormForms();
        }
        if (ExtensionManagementUtility::isLoaded('powermail')) {
            $data['ext-powermail'] = $this->getPowermailForms();
        }
        if (ExtensionManagementUtility::isLoaded('formhandler')) {
            $data['ext-formhandler'] = $this->getFormhandlerForms();
        }

        return $data;
    }

    public function setStatus($type, $id, bool $status)
    {
        $this->registry->set(self::REGISTRY_NAMESPACE, $this->getRegistryKey($type, $id), $status);
    }

    protected function getPowermailForms()
    {
        $queryBuilder = $this->getQueryBuilder('tt_content');
        $rows = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('list', \PDO::PARAM_STR)),
                $queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter('powermail_pi1', \PDO::PARAM_STR))
            )
            ->execute()
            ->fetchAll();

        $rows = $this->enhanceRows('powermail', $rows);
        return $rows;
    }

    protected function getFormhandlerForms()
    {
        $queryBuilder = $this->getQueryBuilder('tt_content');
        $rows = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('list', \PDO::PARAM_STR)),
                $queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter('formhandler_pi1', \PDO::PARAM_STR))
            )
            ->execute()
            ->fetchAll();

        $rows = $this->enhanceRows('formhandler', $rows);
        return $rows;
    }

    protected function getFormForms()
    {
        $queryBuilder = $this->getQueryBuilder('tt_content');
        $rows = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('form_formframework', \PDO::PARAM_STR))
            )
            ->execute()
            ->fetchAll();

        $rows = $this->enhanceRows('form', $rows);
        return $rows;
    }

    protected function enhanceRows(string $type, array $rows)
    {
        foreach ($rows as $key => $row) {
            $status = $this->registry->get(self::REGISTRY_NAMESPACE, $this->getRegistryKey($type, $row['uid']), false);
            $rows[$key]['_meta'] = [
                'type' => $type,
                'isValidated' => $status,
                'links' => [
                    'editContentElement' => $this->createEditUri('tt_content', $row['uid'])
                ],
                'page' => BackendUtility::getRecord('pages', $row['pid']),
                'path' => BackendUtility::getRecordPath($row['pid'], $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW), 1000)
            ];
        }

        return $rows;
    }

    protected function getRegistryKey(string $type, int $id): string
    {
        return sprintf('%s-%s', $type, $id);
    }

    protected function createEditUri(string $table, int $id): string
    {
        $urlParameters = [
            'edit' => [
                $table => [
                    $id => 'edit'
                ]
            ],
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
        ];
        return (string)$this->uriBuilder->buildUriFromRoute('record_edit', $urlParameters);
    }

    /**
     * Returns the current BE user.
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
