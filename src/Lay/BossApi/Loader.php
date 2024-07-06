<?php

namespace Lay\BossApi;

use CortexPE\Commando\PacketHooker;
use Lay\BossApi\commands\SummonBoss;
use Lay\BossApi\entity\examples\EvokerBoss;
use pocketmine\plugin\PluginBase;

final class Loader extends PluginBase {
    
    public function onEnable(): void{
        BossRegistry::register("evoker_boss", EvokerBoss::class);
        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        $this->getServer()->getCommandMap()->register("bossapi", new SummonBoss($this, "summonboss", "Summon a boss specified by the id"));
    }

}