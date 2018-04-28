<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Service;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Tca
{
    /** @var string */
    protected $tableName = '';

    protected $fields = [];

    /**
     * Tca constructor.
     *
     * @param string $tableName
     */
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;

        $GLOBALS['TCA'][$this->tableName]['ctrl']['gdpr'] = [
            'enabled' => true
        ];
    }

    public static function getInstance(string $tableName): Tca
    {
        return GeneralUtility::makeInstance(self::class, $tableName);
    }

    public function addRestriction(string $fieldName)
    {
        $GLOBALS['TCA'][$this->tableName]['ctrl']['gdpr']['restriction_field'] = $fieldName;
        $this->fields[$fieldName] = [
            'label' => 'LLL:EXT:gdpr/Resources/Private/Language/locallang.xlf:field.restriction_field',
            'exclude' => true,
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ];

        return $this;
    }

    public function addRandomization(string $fieldName, array $randomizationOptions)
    {
        $GLOBALS['TCA'][$this->tableName]['ctrl']['gdpr']['randomized_field'] = $fieldName;
        $GLOBALS['TCA'][$this->tableName]['ctrl']['gdpr']['randomize_mapping'] = $randomizationOptions['mapping'];
        $GLOBALS['TCA'][$this->tableName]['ctrl']['gdpr']['randomize_datefield'] = $randomizationOptions['dateField'];
        $GLOBALS['TCA'][$this->tableName]['ctrl']['gdpr']['randomize_expirePeriod'] = $randomizationOptions['expirePeriod'];

        $this->fields[$fieldName] = [
            'label' => 'LLL:EXT:gdpr/Resources/Private/Language/locallang.xlf:field.randomized_field',
            'exclude' => true,
            'config' => [
                'type' => 'check',
                'readOnly' => true,
                'default' => 0
            ]
        ];
        return $this;
    }

    public function add(string $position)
    {
        if (!empty($this->fields)) {
            ExtensionManagementUtility::addTCAcolumns($this->tableName, $this->fields);
            $GLOBALS['TCA'][$this->tableName]['palettes']['paletteGdpr'] = [
                'showitem' => implode(', ', array_keys($this->fields)),
            ];

            ExtensionManagementUtility::addToAllTCAtypes(
                $this->tableName,
                '--palette--;LLL:EXT:gdpr/Resources/Private/Language/locallang.xlf:palette;paletteGdpr,',
                '',
                $position
            );
        }
    }

}
