<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Hooks;

use GeorgRinger\Gdpr\Domain\Model\Dto\Table;
use GeorgRinger\Gdpr\Log\LogManager;
use GeorgRinger\Gdpr\Service\Randomization;
use GeorgRinger\Gdpr\Service\TableInformation;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

class ButtonBarHook
{
    protected $tableName = '';

    /**
     * Get buttons
     *
     * @param array $params
     * @param ButtonBar $buttonBar
     * @return array
     */
    public function getButtons(array $params, ButtonBar $buttonBar)
    {
        $buttons = $params['buttons'];
        if (!$this->isButtonVisible()) {
            return $buttons;
        }

        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        $randomizeStatus = (int)GeneralUtility::_GET('randomize');
        if ($randomizeStatus === 1) {
            $url = GeneralUtility::linkThisScript(['randomize' => 1]);
            $button = $buttonBar->makeLinkButton();
            $button->setIcon($iconFactory->getIcon('actions-synchronize', Icon::SIZE_SMALL));
            $button->setTitle('My custom docHeader button');
            $button->setClasses('t3js-modal-trigger');
            $button->setHref($url);
            $button->setDataAttributes([
                'toggle' => 'tooltip',
                'severity' => 'error',
                'title' => 'Randomize record',
                'content' => 'Should this record be really randomized? Content will be gone forever!',
                'button-ok-text' => 'Randomize'
            ]);

            $buttons[ButtonBar::BUTTON_POSITION_LEFT][4][] = $button;
        }
        return $buttons;
    }

    /**
     * Checks if the popup button should be displayed. Returns false if not.
     * Otherwise returns true.
     *
     * @return bool
     */
    protected function isButtonVisible()
    {
        $visible = false;
        $contentUid = $this->getContentUid();

        if ($contentUid !== null && $GLOBALS['BE_USER']->isAdmin()) {
            $visible = true;
        }

        if ($visible) {
            if ((int)GeneralUtility::_GET('randomize') === 1) {
                $randomizationService = GeneralUtility::makeInstance(Randomization::class, $this->tableName);
                $randomizationService->generateDataForTable();

                $newValues = $randomizationService->generateDataForTable();

                $tableInformation = Table::getInstance($this->tableName);
                $newValues[$tableInformation->getGdprRandomizedField()] = 1;

                $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->tableName);
                $connection->update(
                    $this->tableName,
                    $newValues,
                    [
                        'uid' => $contentUid
                    ]
                );
                $logger = GeneralUtility::makeInstance(LogManager::class);
                $logger->log($this->tableName, $contentUid, LogManager::STATUS_RANDOMIZE);

                $url = GeneralUtility::linkThisScript(['randomize' => 2]);
                HttpUtility::redirect($url);
            }
        }

        return $visible;
    }

    /**
     * Returns the uid of the currently edited content element in backend
     *
     * @return int|null content element uid
     */
    protected function getContentUid()
    {
        $editGetParameters = $this->getEditGetParameters();
        if (!is_array($editGetParameters) || empty($editGetParameters)) {
            return null;
        }

        $contentUid = current(array_keys($editGetParameters));
        if ($editGetParameters[$contentUid] !== 'edit') {
            return null;
        }

        return (int)$contentUid;
    }

    /**
     * Returns the get parameters
     *
     * @return null|array
     */
    protected function getEditGetParameters()
    {
        $editGetParam = GeneralUtility::_GP('edit');
        if (empty($editGetParam)) {
            return null;
        }
        $firstKey = array_keys($editGetParam);
        $tableName = (string)$firstKey[0];
        if (TableInformation::isTableEnabled($tableName)) {
            $this->tableName = $tableName;
            return isset($editGetParam[$tableName]) ? $editGetParam[$tableName] : null;
        }

        return null;
    }

}
