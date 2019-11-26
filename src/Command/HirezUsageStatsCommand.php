<?php

namespace App\Command;

use App\Service\Smite;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HirezUsageStatsCommand extends Command
{
    protected static $defaultName = 'smite:hirez:stats';

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
        $this->setDescription('Get usage statistics from the Hirez servers (NOTE: counts as a request)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $usageArray = $this->smite->getUsage();
        if (!empty($usageArray)) {
            foreach ($usageArray[0] as $key => $value) {
                $output->writeln("{$key} {$value}");
            }
        } else {
            $output->writeln('Call to get usage stats failed.');
        }

        return 0;
    }
}
