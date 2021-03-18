<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Service;

use Faker\Factory;
use GeorgRinger\Gdpr\Domain\Model\Dto\ExtensionConfiguration;
use GeorgRinger\Gdpr\Domain\Model\Dto\Table;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Randomization
{

    /** @var */
    protected $faker;

    /** @var string */
    protected $table;

    public function __construct(string $tableName)
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $locale = $extensionConfiguration->getRandomizerLocale() ?: 'en_US';

        if (class_exists(Environment::class) && !Environment::isComposerMode()
            || class_exists(Bootstrap::class) && !Environment::isComposerMode()
        ) {
            @include 'phar://' . ExtensionManagementUtility::extPath('gdpr') . 'Resources/Private/Php/faker.phar/vendor/autoload.php';
        }

        $this->faker = Factory::create($locale);
        $this->table = $tableName;
    }

    public function generateDataForTable(): array
    {
        $table = Table::getInstance($this->table);

        $mapping = $table->getGdprRandomizeMapping();
        if (empty($mapping)) {
            throw new \UnexpectedValueException(sprintf('No mapping for table %s found', $this->table), 1519306065);
        }
        $newValues = [];

        foreach ($mapping as $field => $fakerProperty) {
            try {
                $newValues[$field] = $this->faker->$fakerProperty;
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException(sprintf('Exception %s for mapping of field "%s" of table "%s"', $e->getMessage(), $field, $this->table), 1519305994);
            }
        }

        return $newValues;
    }

}
