<?php

namespace Lay\BossApi\entity;

use pocketmine\entity\Living;

abstract class MinionEntity extends Living {

    private ?BossEntity $bossOrigin = null;

    public function __construct(BossEntity $bossOrigin){
        
    }

}