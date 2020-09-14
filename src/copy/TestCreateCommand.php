<?php


namespace Tests\_support\Command;

use CodeceptionTestsGenerator\CommandExecutor;
use \Symfony\Component\Console\Command\Command;
use \Codeception\CustomCommandInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;


class TestCreateCommand extends Command implements CustomCommandInterface
{
    use \Codeception\Command\Shared\FileSystem;
    use \Codeception\Command\Shared\Config;

    /**
     * returns the name of the command
     *
     * @return string
     */
    public static function getCommandName()
    {
        return "generate:feature";
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setDefinition(array(
            new InputOption('option', 'o', InputOption::VALUE_NONE, 'Custom command'),
        ));

        parent::configure();
    }

    /**
     * Returns the description for the command.
     *
     * @return string The description for the command
     */
    public function getDescription()
    {
        return "The command create test's feature from postman collection";
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input  An InputInterface instance
     * @param \Symfony\Component\Console\Output\OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new CommandExecutor();
        $command->execute();

        return 0;
    }

}