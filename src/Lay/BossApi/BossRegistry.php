<?php

namespace Lay\BossApi;

use Lay\BossApi\entity\BossEntity;
use Lay\BossApi\exceptions\BossRegistrationException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

final class BossRegistry {

    private static array $bosses = [];

    public static function register(string $nameId, string $class){
        $nameId = strtolower($nameId);
        if(array_key_exists($nameId, self::$bosses)) throw new BossRegistrationException("Id $nameId already been registered");
        if(in_array($class, self::$bosses)) throw new BossRegistrationException("Class $class already been registered");
        if(!in_array(BossEntity::class, class_parents($class))) throw new BossRegistrationException("Class $class does not inherit " . BossEntity::class);
        self::$bosses[$nameId] = $class;
        EntityFactory::getInstance()->register($class, function (World $world, CompoundTag $nbt) use ($class):BossEntity {
            return new $class(EntityDataHelper::parseLocation($nbt, $world));
        }, [$nameId]);
    }

    public static function getAllRegisteredBosses(){
        return self::$bosses;
    }

    public static function getBossClass(string $nameId){
        if(!array_key_exists($nameId, self::$bosses)) return null;
        return self::$bosses[$nameId];
    }

    /**
     * @return BossEntity
     */
    public static function spawnBoss(string $nameId, Location $spawnLocation){
        if(!$class = self::getBossClass($nameId)) return null;
        return new $class($spawnLocation);
    }

}