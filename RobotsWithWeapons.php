<?php
//武器原型以及实现
abstract class IWeaponPrototype
{
	public $color;
    public $weight;//kg
    public $price;
    public $modifiedTimes;
    public $durability;
    abstract function __clone();
}

class Firearm extends IWeaponPrototype
{
	const EQUIPED_BUTTON = 1; 
	public function __construct(){
		$this->color = "black";
		$this->weight = 3.15;
		$this->price = 2300;
		$this->modifiedTimes = 0;
		$this->durability = 100;
		echo 'NOTE:Firearm __construct'.PHP_EOL;
	}
	
	function __clone(){}
}

class Knife extends IWeaponPrototype
{
	const EQUIPED_BUTTON = 3; 
	public function __construct(){
		$this->color = "grey";
		$this->weight = 0.75;
		$this->price = 850;
		$this->modifiedTimes = 0;
		$this->durability = 100;
		echo 'NOTE:Knife __construct'.PHP_EOL;
	}
	function __clone(){}
}

//玩家原型以及实现
abstract class IPlayerPrototype
{
	protected $weaponEquipedOnButton1;
	protected $weaponEquipedOnButton3;
    protected $HP;
    protected $name;
    abstract function __clone();
}

class Robot extends IPlayerPrototype
{
	protected $weaponEquipedOnButton1;
	protected $weaponEquipedOnButton3;
    protected $HP;
    protected $name;
    public function __construct(IWeaponPrototype $weaponOnBtn1,IWeaponPrototype $weaponOnBtn3){
    	$this->weaponEquipedOnButton1 = $weaponOnBtn1;
		$this->weaponEquipedOnButton3 = $weaponOnBtn3;
		$this->HP = 100;
		echo 'NOTE:Robot __construct'.PHP_EOL;
    }
   
    public function setWeaponEquipedOnButton1(IWeaponPrototype $weaponOnBtn1){
    	$this->weaponEquipedOnButton1 = $weaponOnBtn1;
    }
    
    public function setWeaponEquipedOnButton3(IWeaponPrototype $weaponOnBtn3){
		$this->weaponEquipedOnButton3 = $weaponOnBtn3;
    }
    
    public function setName($name){
    	$this->name = $name;
    }
    
    function __clone(){}
}

//创建若干机器人
class Client
{
	private $robot;
	private $firearm;
	private $knife;
	
	public function __construct(){
		$this->firearm = new Firearm();
		$this->knife = new Knife();
		$this->robot = new Robot($this->firearm,$this->knife);
	}
	
	public function createRobot($robotNum = 8){	  
		$robots = [];
		for($robotID = 0;$robotID < $robotNum;$robotID++){
			unset($cloneFirearm);
    		$cloneFirearm = clone $this->firearm;
    		$cloneFirearm->durability = intval($this->randFloat(0.85,2) * $cloneFirearm->durability); 
    		$robotName = 'robotPlayer'.$robotID;
    		
    		unset($cloneRobot);
    		$cloneRobot = clone $this->robot; 		
    		$this->setRobot($cloneRobot,$robotName,$cloneFirearm);	
            $robots[] = $cloneRobot;
    	}
    	return $robots; 
	}
	private function randFloat($min,$max){
		return rand()/getrandmax()*($max-$min)+$min;
	}
	private function setRobot(IPlayerPrototype  &$robot,$name,IWeaponPrototype &$weaponOnBtn1){
		$robot->setName($name);
		$robot->setWeaponEquipedOnButton1($weaponOnBtn1);
	}

}

//具体调用
$robotWorker = new Client();
$robots = $robotWorker->createRobot();
print_r($robots);