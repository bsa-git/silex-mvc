<?php

// app/Console/Commands/DBAL/CreateSchemaCommand.php
namespace Console\Commands\DBAL;

use Models\DBAL\ConfigurableSchema;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CreateSchemaCommand
 * @category Command
 * @package  Fluency\Silex\Doctrine\DBAL\Tools\Console\Command
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
class CreateSchemaCommand extends AbstractDBALCommand {

    /**
     * Configures command
     *
     * @return void
     */
    protected function configure() {
        parent::configure();

        $this->setName('dbal:schema:create')
                ->addOption(
                        'fakedata', null, InputOption::VALUE_NONE, 'Set this parameter to add fake data'
                )
                ->setDescription('Creates the configured database schema')
                ->setHelp(
                        <<<EOT
The <info>dbal:schema:create</info> command creates the default
connections database:

<info>php app/console dbal:schema:create</info>

You can also optionally specify the name of a connection to create the
schema for:

<info>php app/console dbal:schema:create --connection=default</info>
EOT
        );
    }

    /**
     * Executes command
     *
     * @param InputInterface  $input  Command input
     * @param OutputInterface $output Console output
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        
        parent::execute($input, $output);

        if ($this->getContainer()->offsetExists('dbs')) {
            $this->setConnection(
                    $this->getContainer()['dbs'][$input->getOption('connection')]
            );
        }

        $schema = new ConfigurableSchema();
        $config = $this->getContainer()['config']['dbal_schema'][$input->getOption('connection')];

        if (isset($config['tables'])) {
            $schema->createTablesFromConfig($config['tables']);
        }

        $schema->bundleMultipleSchemas($config);

        $sql = $schema->toSql($this->getConnection()->getDatabasePlatform());

        foreach ((array) $sql as $query) {
            $output->write(sprintf('Executing <info>%s</info>', $query));
            $this->getConnection()->executeUpdate($query);
            $output->write(' OK' . "\n");
        }

        // Insert fake data to database
        if ($input->getOption('fakedata')) {
            $this->_insertFakeData($schema, $output);
        }
    }

    /**
     * Insert fake data to tables
     * @param ConfigurableSchema $schema Schema for insert rows to tables
     * @return void
     */
    private function _insertFakeData(ConfigurableSchema $schema, OutputInterface $output) {
        $conn = $this->getConnection();
        $tbNames = array();
        foreach ($schema->getTableNames() as $tbName) {
            $arr = explode('.', $tbName);
            $tbNames[] = $arr[count($arr) - 1];
        }
        $dbname = $this->getParameter('db.name');
        $filePath = BASEPATH . "/app/Resources/fakedata/{$dbname}.yml";
        if (!is_file($filePath)) {
            $this->getContainer()->abort(404, "File \"{$filePath}\" not found.");
        }
        $rows = Yaml::parse(file_get_contents($filePath));
        foreach ($tbNames as $tbName) {
            $tableRows = $rows[$tbName];
            $count = count($tableRows);
            $output->write("Executing <info>Add of {$count} records into a table \"{$tbName}\".</info>");
            foreach ($tableRows as $row) {
                $conn->insert($tbName, $row);
            }
            $output->write(' OK' . "\n");
        }
    }

}
