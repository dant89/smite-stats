<?php

namespace App\Command;

use App\Entity\God;
use App\Entity\GodAbility;
use App\Entity\MatchItem;
use App\Entity\Player;
use App\Service\SmiteService;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\Match;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MatchItemsUpdateCommand extends Command
{
    protected static $defaultName = 'smite:match:items:update';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var SmiteService
     */
    protected $smite;

    public function __construct(EntityManagerInterface $entityManager, SmiteService $smite)
    {
        $this->entityManager = $entityManager;
        $this->smite = $smite;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Update the stored match items');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $matchItemRepo = $this->entityManager->getRepository(MatchItem::class);
        $matchItems = $matchItemRepo->findAll();

        $existingMatchItemsCount = count($matchItems);
        $output->writeln("{$existingMatchItemsCount} existing match items...");

        $existingMatchIds = [];
        /** @var MatchItem $matchItem */
        foreach ($matchItems as $matchItem) {
            if (!in_array($matchItem->getItemId(), $existingMatchIds)) {
                $existingMatchIds[] = $matchItem->getItemId();
            }
        }

        $apiMatchItemsCount = 0;
        $newMatchItemsCount = 0;
        $updatedMatchItemsCount = 0;

        $apiMatchItems = $this->smite->getItems();
        if (!empty($apiMatchItems)) {
            $apiMatchItemsCount = count($apiMatchItems);
            /** @var array $god */
            foreach ($apiMatchItems as $apiMatchItem) {
                if (!in_array($apiMatchItem['ItemId'], $existingMatchIds)) {
                    $newMatchItem = new MatchItem();
                    $this->setMatchItemValues($newMatchItem, $apiMatchItem);
                    $this->entityManager->persist($newMatchItem);
                    $newMatchItemsCount++;
                } else {
                    /** @var $existingMatchItem MatchItem */
                    $existingMatchItem = $matchItemRepo->findOneBy(['itemId' => $apiMatchItem['ItemId']]);
                    $this->setMatchItemValues($existingMatchItem, $apiMatchItem);
                    $this->entityManager->persist($existingMatchItem);
                    $updatedMatchItemsCount++;
                }
            }
            $this->entityManager->flush();
        }

        $output->writeln("{$apiMatchItemsCount} match items scanned...");
        $output->writeln("{$newMatchItemsCount} match items added!");
        $output->writeln("{$updatedMatchItemsCount} match items updated!");

        return 0;
    }

    protected function setMatchItemValues(MatchItem &$matchItem, array $data): void
    {
        $matchItem->setItemId($data['ItemId']);
        $matchItem->setItemName($data['DeviceName']);
        $matchItem->setActive(($data['ActiveFlag'] === 'y' ? 1 : 0));
        $matchItem->setChildItemId($data['ChildItemId']);
        $matchItem->setIconId($data['IconId']);
        $matchItem->setDescription(($data['ItemDescription']['Description'] ?: null));
        $matchItem->setSecondaryDescription($data['ItemDescription']['SecondaryDescription'] ?: null);
        $matchItem->setTier($data['ItemTier']);
        $matchItem->setPrice($data['Price']);
        $matchItem->setRestrictedRoles($data['RestrictedRoles']);
        $matchItem->setRootItemId($data['RootItemId']);
        $matchItem->setShortDescription($data['ShortDesc'] ?: null);
        $matchItem->setStartingItem(($data['StartingItem'] ? 1 : 0));
        $matchItem->setType($data['Type']);
        $matchItem->setIconUrl($data['itemIcon_URL']);
    }
}
