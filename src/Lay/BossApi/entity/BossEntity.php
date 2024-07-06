<?php

namespace Lay\BossApi\entity;

use Lay\BossApi\attacks\BaseAttack;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;

abstract class BossEntity extends Living{

    private bool $showBossBar = false;
    protected array $initialBossName = [];
    private float $bossHealthAnimation = 0;
    private bool $enraged = false;
    protected bool $invulnerable = false;
    private bool $movable = true;
    private int $attackCooldown = 0;
    private ?BaseAttack $currentAttack = null;
    /**@var ProceduralAttack[] $backgroundAttacks Attacks that is happening without being disturbed*/
    private array $backgroundAttacks = [];
    
    public function __construct(Location $location, ?CompoundTag $nbt = null){
        parent::__construct($location, $nbt);
        $this->setMaxHealth($this->getBaseHealth());
        $this->setHealth($this->getBaseHealth());
    }
    
    public function isInvulnerable(){
        return $this->invulnerable;
    }

    public function invulnerable(bool $isInvulnerable = true){
        $this->invulnerable = $isInvulnerable;
        return $this;
    }

    public function isMovable(){
        return $this->movable;
    }

    public function movable(bool $isMovable = true){
        $this->movable = $isMovable;
        return $this;
    }

    protected function entityBaseTick(int $tickDiff = 1): bool{
        if($this->isMovable()) $this->onMovementAvailable();
        $this->sendBossBar($this->getName());
        foreach ($this->backgroundAttacks as $key => $attack) {
            if(!$attack->next()) unset($this->backgroundAttacks[$key]);
        }
        if(!$this->currentAttack?->next()) {
            if($this->attackCooldown <= 0) $this->onAttackAvailable();
            else $this->attackCooldown--;
            $this->currentAttack = null;
        }
        return parent::entityBaseTick($tickDiff);
    }

    /**
     * @param BaseAttack $attack yield the amount of ticks to sleep the attack, if the generator reaches invalid then the attack is removed
     * @param int $attackCooldown Optional, if wanted to increase the attack coooldown
     */
    public function addBackgroundAttack(BaseAttack $attack, int $attackCooldown = 0){
        $this->backgroundAttacks[] = $attack;
        $this->addAttackCooldown($attackCooldown);
        return $this;
    }

    public function addAttackCooldown(int $amountTicks){
        $this->attackCooldown += $amountTicks;
        return $this;
    }

    // Called every tick when the attack cooldown is finished, use this to call a new attack
    protected function onAttackAvailable(): void { }

    // Called when the movement is available every tick
    protected function onMovementAvailable(): void { }

    /**
     * An attack that can have multiple delays but the attack cooldown will not be finished until the attack is finished
     * Useful for long and heavy/extensive attacks
     */
    protected function setAwaitAttack(BaseAttack $attack){
        if($this->currentAttack) return false;
        $this->currentAttack = $attack;
        return true;
    }

    public function summonMinion(){

    }

    public function showBossBar(bool $show = true){
        $this->showBossBar = $show;
        return $this;
    }

    public function canShowBossBar(){
        return $this->showBossBar;
    }

    public function sendBossBar(string $text = "", ?float $health = null){
        $world = $this->getWorld();
        if(!empty($this->initialBossName)){
            $this->invulnerable();
            if($this->ticksLived % 10) return;
            $text = array_shift($this->initialBossName);
        }
        elseif($this->bossHealthAnimation <= 1){
            if($this->ticksLived % 3) return;
            $health = $this->bossHealthAnimation += 0.1;
            if($this->bossHealthAnimation >= 1) $this->invulnerable(false);
        }
        foreach($world->getPlayers() as $player){
            $session = $player->getNetworkSession();
            if($this->showBossBar){
                $session->sendDataPacket(BossEventPacket::show($this->getId(), $text ?? $this->getName(), $this->getHealthPercentage()));
                $session->sendDataPacket(BossEventPacket::title($this->getId(), $text ?? $this->getName()), true);
                $session->sendDataPacket(BossEventPacket::healthPercent($this->getId(), $health ?? $this->getHealthPercentage()), true);
            }else $session->sendDataPacket(BossEventPacket::hide($this->getId()));
        }
    }

    private function getHealthPercentage(){
        return $this->getHealth() / $this->getMaxHealth();
    }

    public function onEnrage(){
        $this->enraged = true;
        return $this;
    }

    public function isEnraged(){
        return $this->enraged;
    }

    public abstract function getBaseHealth(): int;

    public abstract function getBaseAttackDamage(): int;

}