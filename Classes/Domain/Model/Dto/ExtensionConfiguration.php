<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Domain\Model\Dto;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as ExtensionConfigurationCore;
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
        try {
            $settings = GeneralUtility::makeInstance(ExtensionConfigurationCore::class)->get('gdpr');
            if (!empty($settings)) {
                $this->randomizerLocale = $settings['randomizerLocale'];
                $this->overloadMediaRenderer = isset($settings['overloadMediaRenderer']) ? (bool)$settings['overloadMediaRenderer'] : true;
            }
        } catch (\Exception $e) {
            // do nothing
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

    public static function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

}
