<?php

namespace App\Command;

use App\Service\SmiteService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HirezPingCommand extends Command
{
    protected static $defaultName = 'smite:hirez:ping';

    /**
     * @var SmiteService
     */
    protected $smite;

    public function __construct(SmiteService $smite)
    {
        $this->smite = $smite;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Ping the Hirez servers to test connection');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $this->smite->ping();
        if (!empty($data)) {
            var_dump($data);
        }

        return 0;
    }
}
