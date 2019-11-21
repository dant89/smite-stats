<?php

namespace App\Command;

use App\Service\Smite;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateGodsCommand extends Command
{
    protected static $defaultName = 'smite:update-gods';

    /**
     * @var Smite
     */
    protected $smite;

    public function __construct(Smite $smite)
    {
        $this->smite = $smite;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Update the cached Gods');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {



        $gods = $this->smite->getGods();
        foreach ($gods as $god) {
            echo json_encode($god, JSON_PRETTY_PRINT);
            return;
        }
        // TODO query the local database and check each god

    }
}
