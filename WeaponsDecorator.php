<?php

//use abstract class but not interface for these elements in common
abstract class IWeapon
{
    public $color;
    protected $bullet;
    protected $damage;
    protected $sound;

    abstract public function setColor($color);
    abstract public function MouseLeftClickAttack();
    abstract public function MouseRightClickAttack();
    abstract public function getFeatures();
}

abstract class WeaponsDecorator extends IWeapon
{
    //some regular function not changed is able to implement in Decorator
    public function setColor($color)
    {
        $this->color = $color;
    }

    abstract public function MouseLeftClickAttack();
    abstract public function MouseRightClickAttack();
    abstract public function getFeatures();
}

// MainWeapon and SubWeapon are two different Component
class MainWeapon extends IWeapon
{
    public function __construct($bullet = 100, $damage = 75, $sound = 135)
    {
        echo 'MainWeapon:__construct start' . PHP_EOL;
        $this->bullet = $bullet;
        $this->damage = $damage;
        $this->sound = $sound;
        $this->setColor();
        echo 'MainWeapon original getFeatures()' . PHP_EOL;
        $this->getFeatures();
        echo 'MainWeapon:__construct end' . PHP_EOL;
    }

    public function setColor($color = 'black')
    {
        $this->color = $color;
    }

    public function MouseLeftClickAttack()
    {
        if ($this->bullet > 0) {
            echo 'shoot a bullet, damage is ' . $this->damage . PHP_EOL;
            echo 'Gunshots could be heard as ' . $this->sound . PHP_EOL;
            $this->bullet--;
        } else {
            echo 'need to reload' . PHP_EOL;
        }
    }

    public function MouseRightClickAttack()
    {
        echo 'using scope to aim' . PHP_EOL;
    }

    public function getFeatures()
    {
        $this->MouseLeftClickAttack();    //LeftClick to shoot
        $this->MouseRightClickAttack(); //RightClick to aim
    }
}

class SubWeapon extends IWeapon
{
    const SOUND_WITH_SILENCER = 5;
    const SOUND_BASIC = 80;
    protected $isUsingSilencer;

    public function __construct($bullet = 30, $damage = 25, $isUsingSilencer = false)
    {
        echo 'SubWeapon:__construct' . PHP_EOL;
        $this->bullet = $bullet;
        $this->damage = $damage;
        $this->sound = $this::SOUND_BASIC;
        $this->isUsingSilencer = $isUsingSilencer;
        $this->setColor();

    }

    public function setColor($color = 'silver')
    {
        $this->color = $color;
    }

    public function MouseLeftClickAttack()
    {
        if ($this->bullet > 0) {
            echo 'shoot a bullet, damage is ' . $this->damage . PHP_EOL;
            echo 'Gunshots could be heard as ' . $this->sound . PHP_EOL;
            $this->bullet--;
        } else {
            echo 'need to reload' . PHP_EOL;
        }
    }

    public function MouseRightClickAttack()
    {
        if ($this->isUsingSilencer) {
            echo 'removing a silencer' . PHP_EOL;
            $this->sound = $this::SOUND_BASIC;
        } else {
            echo 'attaching a silencer' . PHP_EOL;
            $this->sound = $this::SOUND_WITH_SILENCER;
        }
    }

    public function getFeatures()
    {
        $this->MouseLeftClickAttack();    //LeftClick to shoot
        $this->MouseRightClickAttack(); //RightClick to use/cancel silencer
        $this->MouseLeftClickAttack();    //shoot again
        $this->MouseRightClickAttack();
    }
}

// Decorator add fire buff
class FireBuff extends WeaponsDecorator
{
    const DAMAGE_RATIO_FIRE_BUFF_SHOOT = 1.85;
    const DAMAGE_RATIO_FIRE_BUFF_SKILL = 16;
    const BULLET_FIRE_BUFF_SHOOT_COST = 1;
    const BULLET_FIRE_BUFF_SKILL_COST = 6;
    private $weapon;
    protected $fireBuffBullet;

    // save the constructing $weapon instance then add more features
    // most var is using from $this->weapon
    public function __construct(IWeapon $weapon)
    {
        echo 'WeaponsDecorator: fire-buff' . PHP_EOL;
        $this->weapon = $weapon;
        $this->fireBuffBullet = 100;
    }

    public function setFireBuffBullet($buffBulletNum)
    {
        $this->fireBuffBullet = $buffBulletNum;
    }

    public function MouseLeftClickAttack()
    {
        if ($this->fireBuffBullet > 0) {
            echo 'shoot a fire-bullet, damage is ';
            echo $this::DAMAGE_RATIO_FIRE_BUFF_SHOOT * $this->weapon->damage . PHP_EOL;
            echo 'Gunshots could be heard as ' . $this->weapon->sound . PHP_EOL;
            $this->fireBuffBullet -= $this::BULLET_FIRE_BUFF_SHOOT_COST;
        } else {
            $this->weapon->MouseLeftClickAttack();
        }
    }

    public function MouseRightClickAttack()
    {
        $this->weapon->MouseRightClickAttack();

        if ($this->fireBuffBullet > $this::BULLET_FIRE_BUFF_SKILL_COST) {
            echo 'use fire-buff skill, damage is ';
            echo $this::DAMAGE_RATIO_FIRE_BUFF_SKILL * $this->weapon->damage . PHP_EOL;
            $this->fireBuffBullet -= $this::BULLET_FIRE_BUFF_SKILL_COST;
        } else {
            echo 'not enough fireBuffBullet to use fire-buff skill' . PHP_EOL;
        }
    }

    public function getFeatures()
    {
        $this->MouseLeftClickAttack();
        $this->MouseRightClickAttack();
        $this->MouseLeftClickAttack();
        $this->MouseRightClickAttack();
    }
}

// Decorator add auto-reloading
class AutoReloading extends WeaponsDecorator
{
    private $weapon;
    protected $reloadBulletNum = 30;

    //save the constructing $weapon instance then add more features
    public function __construct(IWeapon $weapon)
    {
        echo 'WeaponsDecorator: auto reloading' . PHP_EOL;
        $this->weapon = $weapon;
    }

    // no implement
    public function MouseLeftClickAttack()
    {
        $this->weapon->MouseLeftClickAttack();
    }

    public function MouseRightClickAttack()
    {
        if ($this->weapon->bullet == 0) {
            $this->weapon->bullet += $this->reloadBulletNum;
        }

        $this->weapon->MouseRightClickAttack();
    }

    public function getFeatures()
    {
        $this->MouseLeftClickAttack();
        $this->MouseRightClickAttack();
        $this->MouseLeftClickAttack();
        $this->MouseRightClickAttack();
    }
}

class Client
{
    private $weapon;

    public function __construct(IWeapon $weapon)
    {
        $this->weapon = $weapon;
        $this->weapon = $this->wrapComponent($this->weapon);
        $this->weapon->getFeatures();
    }

    private function wrapComponent(IWeapon $weapon)
    {
        $component = new FireBuff($weapon);
        $component->setFireBuffBullet(8);
        //$component->setFireBuffBullet(3);
        $component = new AutoReloading($component);
        return $component;
    }
}

$worker = new Client(new MainWeapon(2));
echo PHP_EOL;
$worker = new Client(new SubWeapon(3));

// MainWeapon:__construct start
// MainWeapon original getFeatures()
// shoot a bullet, damage is 75
// Gunshots could be heard as 135
// using scope to aim
// MainWeapon:__construct end
// WeaponsDecorator: fire-buff
// WeaponsDecorator: auto reloading
// shoot a fire-bullet, damage is 138.75
// Gunshots could be heard as 135
// using scope to aim
// use fire-buff skill, damage is 1200
// shoot a fire-bullet, damage is 138.75
// Gunshots could be heard as 135
// using scope to aim
// not enough fireBuffBullet to use fire-buff skill

// SubWeapon:__construct
// WeaponsDecorator: fire-buff
// WeaponsDecorator: auto reloading
// shoot a fire-bullet, damage is 46.25
// Gunshots could be heard as 80
// attaching a silencer
// use fire-buff skill, damage is 400
// shoot a fire-bullet, damage is 46.25
// Gunshots could be heard as 5
// attaching a silencer
// not enough fireBuffBullet to use fire-buff skill

