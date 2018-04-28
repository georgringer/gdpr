<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Controller;

use GeorgRinger\Gdpr\Domain\Model\Dto\LogFilter;
use GeorgRinger\Gdpr\Domain\Model\Dto\Search;
use GeorgRinger\Gdpr\Domain\Model\Dto\Table;
use GeorgRinger\Gdpr\Domain\Repository\FormRepository;
use GeorgRinger\Gdpr\Domain\Repository\LogRepository;
use GeorgRinger\Gdpr\Domain\Repository\RecordRepository;
use GeorgRinger\Gdpr\Service\TableInformation;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class AdministrationController extends ActionController
{

    /** @var BackendTemplateView */
    protected $view;

    /** @var string */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /** @var RecordRepository */
    protected $recordRepository;

    public function initializeAction()
    {
        $this->recordRepository = GeneralUtility::makeInstance(RecordRepository::class);

        if ($this->request->getControllerActionName() !== 'moduleNotEnabled' && (int)$this->getBackendUser()->user['gdpr_module_enable'] === 0) {
            $this->redirect('moduleNotEnabled');
        }
    }

    public function initializeView(ViewInterface $view)
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/Modal');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/DateTimePicker');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/jquery.clearable');
        $dateFormat = ($GLOBALS['TYPO3_CONF_VARS']['SYS']['USdateFormat'] ? ['MM-DD-YYYY', 'HH:mm MM-DD-YYYY'] : ['DD-MM-YYYY', 'HH:mm DD-MM-YYYY']);
        $pageRenderer->addInlineSetting('DateTimePicker', 'DateFormat', $dateFormat);

        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();

        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $buttonList = [
            [
                'action' => 'index',
                'icon' => 'actions-system-list-open',
                'position' => ButtonBar::BUTTON_POSITION_LEFT,
                'group' => 1
            ],
            [
                'action' => 'search',
                'icon' => 'actions-search',
                'position' => ButtonBar::BUTTON_POSITION_LEFT,
                'group' => 1
            ],
            [
                'action' => 'formOverview',
                'icon' => 'form-email',
                'position' => ButtonBar::BUTTON_POSITION_LEFT,
                'group' => 1
            ],
            [
                'action' => 'log',
                'icon' => 'actions-document-open-read-only',
                'position' => ButtonBar::BUTTON_POSITION_LEFT,
                'group' => 2
            ],
            [
                'action' => 'configuration',
                'icon' => 'actions-system-extension-configure',
                'position' => ButtonBar::BUTTON_POSITION_RIGHT,
                'group' => 1
            ],
            [
                'action' => 'help',
                'icon' => 'actions-system-help-open',
                'position' => ButtonBar::BUTTON_POSITION_RIGHT,
                'group' => 2
            ],
        ];

        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        foreach ($buttonList as $buttonDefinition) {
            $button = $buttonBar->makeLinkButton()
                ->setIcon($iconFactory->getIcon($buttonDefinition['icon'], Icon::SIZE_SMALL))
                ->setTitle($buttonDefinition['icon'])
                ->setHref($uriBuilder
                    ->reset()
                    ->setRequest($this->request)->uriFor($buttonDefinition['action'], [], 'Administration'));
            $buttonBar->addButton($button, $buttonDefinition['position'], $buttonDefinition['group']);
        }
    }

    public function indexAction()
    {
        $tables = TableInformation::getAllEnabledTables();

        $collectedRows = [];
        foreach ($tables as $table) {
            $collectedRows[$table]['statistics'] = $this->recordRepository->getStatisticOfTable($table);
            $collectedRows[$table]['rows'] = $this->recordRepository->getRestrictedRows($table);
            $collectedRows[$table]['meta'] = Table::getInstance($table);
        }

        $this->view->assignMultiple([
            'tables' => $tables,
            'restrictedData' => $collectedRows
        ]);
    }

    /**
     * @param string $table
     * @param int $id
     */
    public function deleteAction(string $table, int $id)
    {
        $this->recordRepository->deleteRecord($table, $id);
        $this->addFlashMessage('deleted');
        $this->forward('index');
    }

    /**
     * @param string $table
     * @param int $id
     */
    public function reenableAction(string $table, int $id)
    {
        $this->recordRepository->enableRecord($table, $id);
        $this->addFlashMessage('reenabled');
        $this->forward('index');

    }

    /**
     * @param string $table
     * @param int $id
     */
    public function disableAction(string $table, int $id)
    {
        $this->recordRepository->disableRecord($table, $id);
        $this->addFlashMessage('disabled');
        $this->forward('index');

    }

    /**
     * @param string $table
     * @param int $id
     */
    public function randomizeAction(string $table, int $id)
    {
        $this->recordRepository->randomizeRecord($table, $id);
        $this->addFlashMessage(sprintf('The record with id %d from table "%s" has been randomized', $id, $table));
        $this->forward('index');
    }

    /**
     * @param \GeorgRinger\Gdpr\Domain\Model\Dto\Search $search
     */
    public function searchAction(Search $search = null)
    {
        $searchPerformed = false;
        if ($search === null) {
            $search = $this->objectManager->get(Search::class);
        } else {
            $searchPerformed = true;
        }

        $this->view->assignMultiple([
            'search' => $search,
            'result' => $this->recordRepository->search($search),
            'searchPerformed' => $searchPerformed,
        ]);
    }

    /**
     * @param \GeorgRinger\Gdpr\Domain\Model\Dto\LogFilter $filter
     */
    public function logAction(LogFilter $filter = null)
    {
        if ($filter === null) {
            $filter = $this->objectManager->get(LogFilter::class);
        }

        $allTableNames = [];
        foreach (TableInformation::getAllEnabledTables() as $tableName) {
            $allTableNames[$tableName] = $tableName;
        }

        $this->view->assignMultiple([
            'allTableNames' => $allTableNames,
            'filter' => $filter,
            'result' => GeneralUtility::makeInstance(LogRepository::class)->filter($filter),
        ]);
    }

    public function formOverviewAction()
    {
        $formRepository = GeneralUtility::makeInstance(FormRepository::class);
        $this->view->assignMultiple([
            'forms' => $formRepository->getAllForms(),
        ]);
    }


    public function configurationAction()
    {
        $allTables = TableInformation::getAllEnabledTables();

        $information = [];
        foreach ($allTables as $tableName) {
            $information[$tableName] = Table::getInstance($tableName);
        }

        $this->view->assignMultiple([
            'tables' => $information
        ]);
    }

    /**
     * View which shows information if current user got no access
     */
    public function moduleNotEnabledAction()
    {

    }

    public function helpAction()
    {

    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
