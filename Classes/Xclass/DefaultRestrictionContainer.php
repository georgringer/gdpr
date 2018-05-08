<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Xclass;

use GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DefaultRestrictionContainer extends \TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer
{

    public function __construct()
    {
        parent::__construct();
        $this->addGdprConstraints();
    }

    public function removeAll()
    {
        parent::removeAll();
        $this->addGdprConstraints();

        return $this;
    }

    protected function addGdprConstraints()
    {
        $this->add(GeneralUtility::makeInstance(GdprRestriction::class));
    }
}
