<?php

namespace Lay\BossApi\attacks;

use Lay\BossApi\entity\BossEntity;

abstract class ProceduralAttack extends Attack {
    private int $ticks = 0;

    private \Generator $proceduredAttack;

    public function __construct(BossEntity $baseEntity){
        parent::__construct($baseEntity);
    }

    public function next(){
        if(!$this->proceduredAttack->valid()) return false;
        if(--$this->ticks <= 0){
            $this->proceduredAttack->next();
            if(!$this->proceduredAttack->valid()) return false;
            $this->ticks = $this->proceduredAttack->current();
        }
        return true;
    }

    public function getBaseEntity(){
        return $this->baseEntity;
    }

    public function attack(): void{
        $this->proceduredAttack = $this->baseAttack();
        $this->ticks = $this->proceduredAttack->current();
    }

    /**
     * Can only yield integers or the amount of ticks to sleep
     */
    public abstract function baseAttack(): \Generator;

}