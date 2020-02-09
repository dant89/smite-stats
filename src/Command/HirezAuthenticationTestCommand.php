<?php

namespace App\Command;

use App\Service\SmiteService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HirezAuthenticationTestCommand extends Command
{
    protected static $defaultName = 'smite:hirez:authentication:test';

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
        $this->setDescription('Get usage statistics from the Hirez servers (NOTE: counts as a request)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $usageArray = $this->smite->testHirezSession();
        var_dump($usageArray);

        return 0;
    }
}
