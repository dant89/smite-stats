<?php

namespace App\Command;

use App\Entity\God;
use App\Entity\GodAbility;
use App\Service\Smite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddUnknownGodsCommand extends Command
{
    protected static $defaultName = 'smite:add-unknown-gods';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Smite
     */
    protected $smite;

    public function __construct(EntityManagerInterface $entityManager, Smite $smite)
    {
        $this->entityManager = $entityManager;
        $this->smite = $smite;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Add unknown god IDs to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->entityManager->getRepository(God::class);
        $godAbilityRepo = $this->entityManager->getRepository(GodAbility::class);
        $gods = $repository->findAll();

        $existingGodCount = count($gods);
        $output->writeln("{$existingGodCount} existing gods...");

        $existingGodIds = [];
        /** @var God $god */
        foreach ($gods as $god) {
            if (!in_array($god->getSmiteId(), $existingGodIds)) {
                $existingGodIds[] = $god->getSmiteId();
            }
        }

        $godCount = 0;

        $newGodAbilityCount = 0;
        $newGodCount = 0;

        $updatedGodAbilityCount = 0;
        $updatedGodCount = 0;

        $gods = $this->smite->getGods();
        if (!empty($gods)) {
            $godCount = count($gods);
            /** @var array $god */
            foreach ($gods as $god) {
                if (!in_array($god['id'], $existingGodIds)) {
                    $newGod = new God();
                    $newGod->setSmiteId($god['id']);
                    $this->setGodTraits($newGod, $god);
                    $newGod->setDateCreated(new \DateTime());

                    for ($i=1; $i<=5; $i++) {
                        $godAbility = new GodAbility();
                        $godAbility->setGod($newGod);
                        $this->setGodAbilityTraits($godAbility, $god, $i);
                        $this->entityManager->persist($godAbility);
                        $newGodAbilityCount++;
                    }

                    $this->entityManager->persist($newGod);
                    $newGodCount++;
                } else {
                    /** @var God $existingGod */
                    $existingGod = $repository->findOneBy(['smiteId' => $god['id']]);
                    $existingGodAbilities = $godAbilityRepo->findBy(['god' => $existingGod->getSmiteId()]);

                    $existingGodAbilityIds = [];
                    /** @var GodAbility $existingGodAbility */
                    foreach ($existingGodAbilities as $existingGodAbility) {
                        if (!in_array($existingGodAbility->getAbilityId(), $existingGodAbilityIds)) {
                            $existingGodAbilityIds[] = $existingGodAbility->getAbilityId();
                        }
                    }

                    for ($i = 1; $i <= 5; $i++) {
                        if (!in_array($god["Ability_{$i}"]["Id"], $existingGodAbilityIds)) {
                            $godAbility = new GodAbility();
                            $godAbility->setGod($existingGod);
                            $this->setGodAbilityTraits($godAbility, $god, $i);
                            $this->entityManager->persist($godAbility);
                            $updatedGodAbilityCount++;
                        }
                    }

                    $this->setGodTraits($existingGod, $god);
                    $this->entityManager->persist($existingGod);
                    $updatedGodCount++;
                }
            }
            $this->entityManager->flush();
        }

        $output->writeln("{$godCount} gods scanned...");
        $output->writeln("{$newGodCount} gods added!");
        $output->writeln("{$newGodAbilityCount} god abilities added!");
        $output->writeln("{$updatedGodCount} gods updated!");
        $output->writeln("{$updatedGodAbilityCount} god abilities updated!");

        return 0;
    }

    protected function setGodTraits(God &$god, array $data): void
    {
        $god->setName($data['Name']);
        $god->setAttackSpeed($data['AttackSpeed']);
        $god->setAttackSpeedPerLevel($data['AttackSpeedPerLevel']);
        $god->setCons($data['Cons']);
        $god->setHp5PerLevel($data['HP5PerLevel']);
        $god->setHealth($data['Health']);
        $god->setHealthPerFive($data['HealthPerFive']);
        $god->setHealthPerLevel($data['HealthPerLevel']);
        $god->setLore($data['Lore']);
        $god->setMp5PerLevel($data['MP5PerLevel']);
        $god->setMagicProtection($data['MagicProtection']);
        $god->setMagicProtectionPerLevel($data['MagicProtectionPerLevel']);
        $god->setMagicalPower($data['MagicalPower']);
        $god->setMagicalPowerPerLevel($data['MagicalPowerPerLevel']);
        $god->setMana($data['Mana']);
        $god->setManaPerFive($data['ManaPerFive']);
        $god->setManaPerLevel($data['ManaPerLevel']);
        $god->setOnFreeRotation($data['OnFreeRotation']);
        $god->setPantheon($data['Pantheon']);
        $god->setPhysicalPower($data['PhysicalPower']);
        $god->setPhysicalPowerPerLevel($data['PhysicalPowerPerLevel']);
        $god->setPhysicalProtection($data['PhysicalProtection']);
        $god->setPhysicalProtectionPerLevel($data['PhysicalProtectionPerLevel']);
        $god->setPros($data['Pros']);
        $god->setRoles($data['Roles']);
        $god->setSpeed($data['Speed']);
        $god->setTitle($data['Title']);
        $god->setType($data['Type']);
        $god->setCardUrl($data['godCard_URL']);
        $god->setIconUrl($data['godIcon_URL']);
        $god->setDateUpdated(new \DateTime());
    }

    protected function setGodAbilityTraits(GodAbility &$godAbility, array $data, int $abilityNumber): void
    {
        $godAbility->setAbilityId($data["Ability_{$abilityNumber}"]["Id"]);
        $godAbility->setAbilityNumber($abilityNumber);
        $godAbility->setDescription($data["Ability_{$abilityNumber}"]["Description"]["itemDescription"]["description"]);
        $godAbility->setCooldown($data["Ability_{$abilityNumber}"]["Description"]["itemDescription"]["cooldown"]);
        $godAbility->setCost($data["Ability_{$abilityNumber}"]["Description"]["itemDescription"]["cost"]);
        $godAbility->setSummary($data["Ability_{$abilityNumber}"]["Summary"]);
        $godAbility->setUrl($data["Ability_{$abilityNumber}"]["URL"]);
    }
}
