<?php

namespace Lay\BossApi\attacks;

use Lay\BossApi\entity\BossEntity;

abstract class BaseAttack {
    private int $ticks = 0;

    private \Generator $attack;

    public function __construct(protected BossEntity $baseEntity){
        $this->attack = $this->baseAttack();
        $this->ticks = $this->attack->current();
    }

    public function next(){
        if(!$this->attack->valid()) return false;
        if(--$this->ticks <= 0){
            $this->attack->next();
            if(!$this->attack->valid()) return false;
            $this->ticks = $this->attack->current();
        }
        return true;
    }

    public function getBaseEntity(){
        return $this->baseEntity;
    }

    /**
     * Can only yield integers or the amount of ticks to sleep
     */
    public abstract function baseAttack(): \Generator;

}