<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Command;

use GeorgRinger\Gdpr\Log\LogManager;
use GeorgRinger\Gdpr\Service\IpAnonymizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AnonymizeIpCommand extends Command
{

    const DEFAULT_AGE = 300;

    /** @var LogManager */
    protected $logger;

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $description = 'Anonymize IP address of given rows';

        $this
            ->setDescription($description)
            ->addArgument('table', InputArgument::REQUIRED, 'table name')
            ->addArgument('ageField', InputArgument::REQUIRED, 'Name of field with age')
            ->addArgument('ipField', InputArgument::REQUIRED, 'field holding the IP')
            ->addArgument('age', InputArgument::REQUIRED, 'Age in days');
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

        $table = $input->getArgument('table');
        $allTables = array_keys($GLOBALS['TCA']);
        if (!in_array($table, $allTables, true)) {
            $io->warning(sprintf(
                'The table "%s" is not available for randomization! Available tables: %s',
                $table,
                implode(', ', $allTables)
            ));
            return 0;
        }

        $targetField = $input->getArgument('ipField');
        $ageField = $input->getArgument('ageField');
        $allFields = array_keys($GLOBALS['TCA'][$table]['columns']);
        if (!in_array($targetField, $allFields, true)) {
            $io->warning(sprintf(
                'The table "%s" does not contain the field "%s"! Available fields: %s',
                $table,
                $targetField,
                implode(', ', $allFields)
            ));
            return 0;
        }
        if (!in_array($ageField, $allFields, true)) {
            $io->warning(sprintf(
                'The table "%s" does not contain the field "%s"! Available fields: %s',
                $table,
                $ageField,
                implode(', ', $allFields)
            ));
            return 0;
        }

        $age = (int)$input->getArgument('age');
        if ($age === 0) {
            $io->warning(sprintf('No proper age given'));
            return 0;
        }

        $io->section(sprintf('Starting with table "%s", IP field "%s", age field "%s" and older than %d days', $table, $targetField, $ageField, $ageField));
        $this->update($table, $targetField, $ageField, $age);
        return 0;
    }

    private function update(string $table, string $targetField, string $ageField, int $age)
    {
        $timestamp = strtotime('-' . $age . ' days');

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
        $queryBuilder = $connection->createQueryBuilder();

        $result = $queryBuilder
            ->select('uid', $targetField)
            ->where(
                $queryBuilder->expr()->lt(
                    $ageField,
                    $queryBuilder->createNamedParameter($timestamp, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->neq(
                    $targetField,
                    $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)
                ),
                $queryBuilder->expr()->notLike(
                    $targetField,
                    $queryBuilder->createNamedParameter('%.0.0', \PDO::PARAM_STR)
                ),
                $queryBuilder->expr()->notLike(
                    $targetField,
                    $queryBuilder->createNamedParameter('%::', \PDO::PARAM_STR)
                )
            )
            ->from($table)
            ->execute();

        while ($row = $result->fetch()) {
            $ip = (string)$row[$targetField];

            $connection->update(
                $table,
                [
                    $targetField => IpAnonymizer::anonymizeIp($ip)
                ],
                [
                    'uid' => $row['uid']
                ]
            );
            $this->logger->log($table, $row['uid'], LogManager::STATUS_IP_ANONYMIZE);
        }
    }

}
