<?php
// app/Console/Commands/DBAL/CreateDatabaseCommand.php
/**
 * PHP version ~5.5
 *
 * @category Command
 * @package  Fluency\Silex\Doctrine\DBAL\Tools\Console\Command
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */

namespace Console\Commands\DBAL;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateDatabaseCommand
 * @category Command
 * @package  Fluency\Silex\Doctrine\DBAL\Tools\Console\Command
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
class CreateDatabaseCommand extends AbstractDBALCommand
{
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('dbal:database:create')
            ->setDescription('Creates the configured database')
            ->setHelp(
                <<<EOT
The <info>dbal:database:create</info> command creates the default
connections database:

<info>php app/console dbal:database:create</info>

You can also optionally specify the name of a connection to create the
database for:

<info>php app/console dbal:database:create --connection=default</info>
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
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        parent::execute($input, $output);

        $params = $this->getConnectionParams($input->getOption('connection'));

        $databaseName = isset($params['path']) && $params['driver'] == 'pdo_sqlite' ?
            $params['path'] : $params['dbname'];

        // Only quote if we don't have a path
        if ($params['driver'] != 'pdo_sqlite') {
            $databaseName = $this->getConnection()->getDatabasePlatform()
                ->quoteSingleIdentifier($databaseName);
        }

        try {
            $this->getConnection()->getSchemaManager()
                ->createDatabase($databaseName);
            $output->writeln(
                sprintf(
                    '<info>Created database <comment>%s</comment> for connection ' .
                    '<comment>%s</comment></info>',
                    $databaseName, $input->getOption('connection')
                )
            );
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            $output->writeln(
                sprintf(
                    '<error>Could not create database <comment>%s</comment> '.
                    'for connection <comment>%s</comment></error>',
                    $databaseName, $input->getOption('connection')
                )
            );
            throw $e;
        }

        $this->getConnection()->close();
    }
}