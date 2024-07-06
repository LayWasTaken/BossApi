<?php

namespace Lay\BossApi\entity\examples;

use Lay\BossApi\entity\BossEntity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

final class EvokerBoss extends BossEntity {

    public static function getNetworkTypeId(): string{
        return EntityIds::EVOCATION_ILLAGER;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.8, 0.6);
    }

    public function getName(): string{
        return "EvokerBoss";
    }

    public function getBaseAttackDamage(): int{
        return 20;
    }

    public function getBaseHealth(): int{
        return 200;
    }

}