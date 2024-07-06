<?php

namespace Lay\BossApi\commands;

use CortexPE\Commando\args\StringEnumArgument;
use CortexPE\Commando\args\Vector3Argument;
use CortexPE\Commando\BaseCommand;
use Lay\BossApi\BossRegistry;
use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

final class SummonBoss extends BaseCommand {

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        if(!$sender instanceof Player) return;
        $boss = BossRegistry::spawnBoss($args["BossIdsArgument"], Location::fromObject($args["PositionArgument"], $sender->getWorld(), mt_rand(0, 35) * 100));
        $boss->showBossBar();
        $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(fn() => $boss->spawnToAll()), 2);
    }

    protected function prepare(): void{
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
        $this->registerArgument(0, new BossIdsArguments("BossIdsArgument"));
        $this->registerArgument(1, new Vector3Argument("PositionArgument"));
    }

}

final class BossIdsArguments extends StringEnumArgument {

    public function getEnumName(): string{
        return "BossIdsArgument";
    }

    public function getTypeName(): string{
        return "Boss Ids";
    }

    public function parse(string $argument, CommandSender $sender): mixed{
        return $argument;
    }

    public function getValue(string $string) {
		return BossRegistry::getAllRegisteredBosses()[strtolower($string)];
	}

	public function getEnumValues(): array {
		return array_keys(BossRegistry::getAllRegisteredBosses());
	}

}