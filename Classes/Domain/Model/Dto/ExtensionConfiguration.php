<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Domain\Model\Dto;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionConfiguration implements SingletonInterface
{

    /** @var string */
    protected $randomizerLocale = 'en_US';

    /** @var bool */
    protected $overloadMediaRenderer = true;

    public function __construct()
    {
        $settings = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['gdpr'], ['allowed_classes' => false]);
        if (!empty($settings)) {
            $this->randomizerLocale = $settings['randomizerLocale'];
            $this->overloadMediaRenderer = isset($settings['overloadMediaRenderer']) ? (bool)$settings['overloadMediaRenderer'] : true;
        }
    }

    /**
     * @return string
     */
    public function getRandomizerLocale(): string
    {
        return $this->randomizerLocale;
    }

    /**
     * @return bool
     */
    public function getOverloadMediaRenderer(): bool
    {
        return $this->overloadMediaRenderer;
    }

    public static function getInstance(): ExtensionConfiguration
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

}
