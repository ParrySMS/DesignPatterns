<?php
//P150 todo: IWeapon做接口 MainWeapon SubWeapon 做组件 Decorator装饰器来实现枪支升级后的新效果

//use abstract class but not interface for these elements in common
abstract class IWeapon
{
    protected $bullet;
    protected $damage;
    protected $sound;

    abstract public function MouseLeftClickAttack();

    abstract public function MouseRightClickAttack();
}

abstract class WeaponsDecorator extends IWeapon
{
    abstract public function MouseLeftClickAttack();

    abstract public function MouseRightClickAttack();
}

class MainWeapon extends IWeapon
{
    public function __construct($bullet = 100, $damage = 75, $sound = 135)
    {
        echo 'MainWeapon:' . PHP_EOL;
        $this->bullet = $bullet;
        $this->damage = $damage;
        $this->sound = $sound;
        $this->MouseLeftClickAttack();    //LeftClick to shoot
        $this->MouseRightClickAttack(); //RightClick to aim
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
}

class SubWeapon extends IWeapon
{
    const SOUND_WITH_SILENCER = 30;
    const SOUND_BASIC = 80;
    protected $isUsingSilencer;

    public function __construct($bullet = 30, $damage = 25, $isUsingSilencer = false)
    {
        echo 'SubWeapon:' . PHP_EOL;
        $this->bullet = $bullet;
        $this->damage = $damage;
        $this->sound = SOUND_BASIC;
        $this->isUsingSilencer = $isUsingSilencer;
        $this->MouseLeftClickAttack();    //LeftClick to shoot
        $this->MouseRightClickAttack(); //RightClick to use/cancel silencer
        $this->MouseLeftClickAttack();    //shoot again
        $this->MouseRightClickAttack();
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
        if($this->isUsingSilencer){
            echo 'removing a silencer' . PHP_EOL;
            $this->sound = SOUND_BASIC;
        }else{
            echo 'attaching a silencer' . PHP_EOL;
            $this->sound = SOUND_WITH_SILENCER;
        }
    }
}

class MainFireBulletsWeapon extends WeaponsDecorator{
    public function MouseLeftClickAttack()
    {
        // TODO: Implement MouseLeftClickAttack() method.
    }

    public function MouseRightClickAttack()
    {
        // TODO: Implement MouseRightClickAttack() method.
    }
}

class MainAutoReloadingWeapon extends WeaponsDecorator{
    public function MouseLeftClickAttack()
    {
        // TODO: Implement MouseLeftClickAttack() method.
    }

    public function MouseRightClickAttack()
    {
        // TODO: Implement MouseRightClickAttack() method.
    }
}