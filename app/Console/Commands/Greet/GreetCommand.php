<?php

namespace Console\Commands\Greet;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GreetCommand
 * 
 * @category Command
 * @package  app\Console\Commands
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class GreetCommand extends \Console\Commands\BaseCommand {

    /**
     * Constructor
     * 
     * @param string $name
     */
    public function __construct($name = null) {
        parent::__construct($name);
    }

    protected function configure() {
        
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        //-------------------

        $this->init($input);

        if ($this->params['name']) {
            $text = "Hello {$this->params['name']}";
        } else {
            $text = 'Hello';
        }

        if ($this->opts['yell']) {
            $text = strtoupper($text);
        }

//        $this->mail(\Swift_Message::newInstance()
//                        ->setSubject('Silex-Console Email Test')
//                        ->setFrom(array('m5-asutp@azot.ck.ua'))
//                        ->setTo(array('bsa2657@yandex.ru'))
//                        ->setBody('Hellow from Console!'));

        $this->saveResults(array($text), TRUE);

        $contex = $this->showResults(array($text), TRUE);
        $output->writeln($contex);
    }

}
