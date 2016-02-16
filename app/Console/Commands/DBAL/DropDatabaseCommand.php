<?php

// app/Console/Commands/DBAL/DropDatabaseCommand.php
namespace Console\Commands\DBAL;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DropDatabaseCommand
 * @category Command
 * @package  Fluency\Silex\Doctrine\DBAL\Tools\Console\Command
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
class DropDatabaseCommand extends AbstractDBALCommand {

    /**
     * Configures command
     *
     * @return void
     */
    protected function configure() {

        // Config base command
        parent::configure();

        // Set params command
        $this->setName('dbal:database:drop')
                ->addOption(
                        'force', null, InputOption::VALUE_NONE, 'Set this parameter to execute this action'
                )
                ->setDescription('Drops the configured database')
                ->setHelp(
                        <<<EOT
The <info>dbal:database:drop</info> command drops the default connections
database:

<info>php app/console dbal:database:drop</info>

The --force parameter has to be used to actually drop the database.

You can also optionally specify the name of a connection to drop the database
for:

<info>php app/console dbal:database:drop --connection=default</info>

<error>Be careful: All data in a given database will be lost when executing
this command.</error>
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

        // Execute base command
        parent::execute($input, $output);

        $params = $this->getConnectionParams($input->getOption('connection'));

        $databaseName = isset($params['path']) && $params['driver'] == 'pdo_sqlite' ?
                $params['path'] : $params['dbname'];

        if (!$databaseName) {
            throw new \InvalidArgumentException(
            'Connection does not contain a \'path\' or \'dbname\' ' .
            'parameter and cannot be dropped.'
            );
        }

        if ($input->getOption('force')) {
            // Only quote if we don't have a path
            if ($params['driver'] != 'pdo_sqlite') {
                $databaseName = $this->getConnection()
                        ->getDatabasePlatform()
                        ->quoteSingleIdentifier($databaseName);
            }

            try {
                $this->getConnection()
                        ->getSchemaManager()
                        ->dropDatabase($databaseName);
                $output->writeln(
                        sprintf(
                                '<info>Dropped database <comment>%s</comment> ' .
                                'for connection <comment>%s</comment></info>', $databaseName, $input->getOption('connection')
                        )
                );
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
                $output->writeln(
                        sprintf(
                                '<error>Could not drop database <comment>%s</comment> ' .
                                'for connection <comment>%s</comment></error>', $databaseName
                        )
                );
                throw $e;
            }
        } else {
            $output->writeln(
                    '<error>Attention:</error> This operation should not be executed ' .
                    'in a production environment.'
            );
            $output->writeln('');
            $output->writeln(
                    sprintf(
                            '<info>Would drop the database named ' .
                            '<comment>%s</comment>.</info>', $databaseName
                    )
            );
            $output->writeln('Please run the operation with --force to execute');
            $output->writeln('<error>All data will be lost!</error>');
        }

        $this->getConnection()->close();
    }

}
