<?php

namespace Lay\BossApi\commands;

use CortexPE\Commando\args\StringEnumArgument;
use CortexPE\Commando\BaseCommand;
use Lay\BossApi\BossRegistry;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;

final class SummonBoss extends BaseCommand {

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        
    }

    protected function prepare(): void{
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
        $this->registerArgument(0, new BossIdsArguments("BossIdsArgument"));
    }

}

final class BossIdsArguments extends StringEnumArgument {

    public function getEnumName(): string{
        return "BossIdsArgument";
    }

    public function getTypeName(): string{
        return "bossids";
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