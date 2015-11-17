<?php

// app/Console/Commands/ErrorCommand.php

namespace Console\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class - ErrorCommand
 * Error handling
 * 
 * @category Command
 * @package  app\Console\Commands
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://site.my
 */
class ErrorCommand extends BaseCommand {

    /**
     * Configures command
     *
     * @return void
     */
    protected function configure() {
        
    }

    /**
     * Executes command
     *
     * @param InputInterface  $input  Command input
     * @param OutputInterface $output Console output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->init($input);

        $message = $this->params["message"];
        $message = base64_decode($message);

        if ($this->opts['color']) {
            $formatter = $this->getHelper('formatter');

            $errorMessages = array($message);
            $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
            $output->writeln($formattedBlock);
        } else {
            $output->writeln($message);
        }
    }

}
