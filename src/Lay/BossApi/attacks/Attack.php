<?php

namespace Lay\BossApi\attacks;

use Lay\BossApi\entity\BossEntity;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;

abstract class Attack {

    public function __construct(protected BossEntity $entity, int $attackCooldown = 0){
        $entity->addAttackCooldown($attackCooldown);
    }

    public function damageTarget(Entity $target){
        if(!$this->entity->isAlive()) return;
        $target->attack(new EntityDamageEvent($this->entity, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->entity->getBaseAttackDamage()));
    }

}