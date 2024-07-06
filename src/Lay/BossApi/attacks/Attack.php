<?php

namespace Lay\BossApi\attacks;

use Lay\BossApi\entity\BossEntity;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\EntityDamageEvent;

abstract class Attack {

    public function __construct(protected BossEntity $baseEntity, int $attackCooldown = 0){
        $baseEntity->addAttackCooldown($attackCooldown);
    }

    public function baseDamageTarget(Entity $target, float $multiplier = 1){
        $this->damageTarget($target, $this->baseEntity->getBaseAttackDamage() * $multiplier);
    }

    public function damageTarget(Entity $target, float $damage){
        if(!$this->baseEntity->isAlive()) return;
        $target->attack(new EntityDamageEvent($this->baseEntity, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $damage));
    }

    public function throwProjectile(Throwable $projectile){
    }

    public abstract function attack(): void;

}