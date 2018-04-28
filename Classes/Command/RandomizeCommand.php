<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Command;


use GeorgRinger\Gdpr\Database\Query\Restriction\GdprRandomizedRestriction;
use GeorgRinger\Gdpr\Domain\Model\Dto\Table;
use GeorgRinger\Gdpr\Log\LogManager;
use GeorgRinger\Gdpr\Service\Randomization;
use GeorgRinger\Gdpr\Service\TableInformation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RandomizeCommand extends Command
{
    /** @var LogManager */
    protected $logger;

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $description = 'Randomize data';
        $this->setDescription($description);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \InvalidArgumentException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class);

        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $allAvailableTables = TableInformation::getAllEnabledTables();

        foreach ($allAvailableTables as $tableName) {
            $io->section(sprintf('Starting with table "%s"', $tableName));

            $tableInformation = Table::getInstance($tableName);
            if (!$tableInformation->randomizationEnabled()) {
                $io->comment('Randomization skipped as not enabled!');
                continue;
            }

            $count = $this->randomizeTable($io, $tableInformation);
            if ($count === 0) {
                $io->comment('No records found');
            } else {
                $io->success(sprintf('%d records randomized', $count));
            }
        }
    }

    /**
     * @param SymfonyStyle $io
     * @param Table $tableInformation
     * @return int count of records
     */
    protected function randomizeTable(SymfonyStyle $io, Table $tableInformation): int
    {
        $table = $tableInformation->getTableName();
        $randomizationService = GeneralUtility::makeInstance(Randomization::class, $table);
        $field = $tableInformation->getGdprRandomizedDateField();

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(GdprRandomizedRestriction::class));

        $period = $tableInformation->getGdprExpirePeriod();
        $timestamp = strtotime('-' . $period . ' days');

        $io->comment(sprintf('find all fields where value of field "%s" is older than %s days', $field, $period));

        $res = $queryBuilder
            ->select('uid')
            ->from($tableInformation->getTableName())
            ->where(
                $queryBuilder->expr()->lt(
                    $field,
                    $queryBuilder->createNamedParameter($timestamp, \PDO::PARAM_INT)
                )
            )->execute();

        $count = 0;

        while ($row = $res->fetch()) {
            $count++;
            $newValues = $randomizationService->generateDataForTable();

            // @todo: should this be configurable?
            $newValues[$tableInformation->getGdprRestrictionField()] = 0;
            $newValues[$tableInformation->getGdprRandomizedField()] = 1;

            $this->getConnection($table)->update(
                $table,
                $newValues,
                [
                    'uid' => $row['uid']
                ]
            );
            $this->logger->log($table, $row['uid'], LogManager::STATUS_RANDOMIZE);
        }

        return $count;
    }

    /**
     * @param string $tableName
     * @return Connection
     */
    private function getConnection(string $tableName): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);
    }

}
