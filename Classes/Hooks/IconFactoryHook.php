<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Hooks;


use GeorgRinger\Gdpr\Domain\Model\Dto\Table;
use GeorgRinger\Gdpr\Service\TableInformation;

class IconFactoryHook
{

    public function postOverlayPriorityLookup($table, array $row, array $status, $iconName)
    {
        if (TableInformation::isTableEnabled($table)) {
            $table = Table::getInstance($table);
            if ($table->randomizationEnabled() && $row[$table->getGdprRandomizedField()]) {
                $iconName = 'overlay-locked';
            }
        }

        return $iconName;
    }

}