<?php

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class DBDiffCommand extends ContainerAwareCommand
{
    private const DIR =  __DIR__ . '/../../../var/cache/db_diff';
    private const DATA_FILE = 'data.json';
    private const DATA_PATH = self::DIR . '/' . self::DATA_FILE;

    public const OPTION_ALL = 'all';

    /** @var OutputInterface */
    private $output;

    protected function configure()
    {
        $this
            ->setName('app:db-diff')
            ->setDescription('Apply expired propositions')
            ->addOption(self::OPTION_ALL, 'a', InputOption::VALUE_NONE, 'Show all tables including unchanged')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentState = $this->getCountRowsInTables();

        $oldState = $this->readData();

        $this->output = $output;

        $this->saveData($currentState);
        if ($oldState) {
            $this->printDiff($oldState, $currentState, [self::OPTION_ALL => $input->getOption(self::OPTION_ALL)]);
        } else {
            $output->writeln('Old data is missing. Run the script again after the tested action.');
        }
    }

    /**
     * @param array $oldState
     * @param array $currentState
     * @param array $options
     */
    private function printDiff($oldState, $currentState, $options = [self::OPTION_ALL => false])
    {
        $tables = array_unique(array_merge(array_keys($oldState), array_keys($currentState)));
        sort($tables);

        $buffer = new BufferedOutput();
        $tableOutput = new Table($buffer);

        $rows = [];
        foreach ($tables as $table) {
            if (isset($oldState[$table]) && isset($currentState[$table])) {
                $diff = $currentState[$table] - $oldState[$table];
                if ($diff === 0) {
                    if ($options[self::OPTION_ALL]) {
                        $rows[] = [$table, 0];
                    }
                } elseif ($diff > 0) { // INSERT
                    $rows[] = [$table, "\e[0;32m".$diff."\e[0m"];
                } elseif($diff) {  // DELETE
                    $rows[] = [$table, "\e[0;31m".$diff."\e[0m"];
                }
            } elseif (isset($oldState[$table])) {
                $rows[] = [$table, "\e[0;31m".$oldState[$table]." (The table has been deleted.)\e[0m"];
            } else {
                $rows[] = [$table, "\e[0;32m".$currentState[$table]." (New table)\e[0m"];
            }
        }

        if (!$options[self::OPTION_ALL] && empty($rows)) {
            $this->output->writeln('No changes');
        } else {
            $tableOutput->setRows($rows);
            $tableOutput->render();
            $this->output->writeln($buffer->fetch());
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array
     */
    private function getCountRowsInTables()
    {
        $arrayData = [];
        $connection = $this->getContainer()->get('doctrine.orm.entity_manager')->getConnection();
        $tables = $connection->executeQuery('SHOW TABLES')->fetchAll();
        $dbName = $connection->getParams()['dbname'];
        $field = 'Tables_in_' . $dbName;

        foreach ($tables as $table) {
            $table = $table[$field];
            $countRows = $connection->executeQuery('SELECT COUNT(*) as \'count\' FROM ' . $table)->fetch();
            $arrayData[$table] = $countRows['count'];
        }

        return $arrayData;
    }


    /**
     * @param array $array
     */
    private function saveData($array)
    {
        if (!file_exists(self::DIR)) {
            mkdir(self::DIR);
        }
        if (file_put_contents(self::DATA_PATH, json_encode($array, JSON_UNESCAPED_UNICODE)) === false) {
            throw new \LogicException('Error creating file: ' . self::DATA_FILE);
        }
    }

    /**
     * @return array|null
     */
    private function readData()
    {
        if (!file_exists(self::DATA_PATH)) {
            return null;
        }

        $data = file_get_contents(self::DATA_PATH);
        if ($data === false) {
            return null;
        } else {
            return json_decode($data, true);
        }
    }
}

