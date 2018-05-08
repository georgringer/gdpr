<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Service;

class TableInformation
{

    public static function isTableEnabled(string $table): bool
    {
        if (isset($GLOBALS['TCA'][$table])
            && is_array($GLOBALS['TCA'][$table]['ctrl']['gdpr'])
            && $GLOBALS['TCA'][$table]['ctrl']['gdpr']['enabled']) {
            return true;
        }
        return false;
    }

    public static function getAllEnabledTables(): array
    {
        $tables = [];

        foreach (array_keys($GLOBALS['TCA']) as $tableName) {
            if (self::isTableEnabled($tableName)) {
                $tables[] = $tableName;
            }
        }
        return $tables;
    }

    public static function getMetaInformationOfTable(string $table): array
    {
        $tca = $GLOBALS['TCA'][$table];
return $tca;
        return [
            'title' => $tca['ctrl']['title'],
            'labelField' => $tca['ctrl']['label']
        ];
    }
}
