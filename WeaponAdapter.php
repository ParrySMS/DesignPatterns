<?php
//武器原型以及实现
interface IWeaponPrototype
{
	public function MouseLeftClickAttack();
	public function MouseRightClickAttack();
}

class Knife implements IWeaponPrototype
{
	public $color;
    public $weight;//kg
    public $price;
    public $modifiedTimes;
    public $durability;

	public function __construct(){
		$this->color = "grey";
		$this->weight = 0.75;
		$this->price = 850;
		$this->modifiedTimes = 0;
		$this->durability = 100;
	}
	public function MouseLeftClickAttack(){ echo 'Knife cutting'.PHP_EOL;}
	public function MouseRightClickAttack(){
		echo 'Knife stabbing'.PHP_EOL;
		$this->durability--;
	}
}

//防护盾原型以及实现
interface IShieldPrototype
{
	public function pushing();
	public function cover();
	public function remove();
}

class RectangleShield implements IShieldPrototype
{
	public $color;
    public $weight;//kg
    public $price;
    public $durability;
	protected $isCovered;
	
	public function __construct(){
		$this->color = "black";
		$this->weight = 5.95;
		$this->price = 1050;
		$this->durability = 1000;
		$this->isCovered = false;
	}
	
	public function getIsCovered(){
		return $this->isCovered;
	}
	
	public function pushing(){
		if($this->isCovered){
			echo 'shield pushing'.PHP_EOL;
		}else{
			echo 'uable to pushing, need shield covered'.PHP_EOL;
		}	
	}
	
	public function cover(){
		$this->isCovered = true;
		echo 'shield covering'.PHP_EOL;
	}
	
	public function remove(){
		$this->isCovered = false;
		echo 'shield removing'.PHP_EOL;
	}
	
	function __clone(){
		echo 'NOTE:RectangleShield __clone'.PHP_EOL;
	}
}

//这是两个基本不同的接口实现 但实际使用中要按照武器的操作方式来用防具 只有一套操作逻辑
//创建一个适配器 接收防具对象来实现武器  直接override
class ShieldAdapter implements IWeaponPrototype
{
	public $color;
    public $weight;//kg
    public $price;
    public $modifiedTimes;
    public $durability;
	private $shield;
	public function __construct(IShieldPrototype $shield){
		$this->shield = $shield;
		$this->color = $shield->color;
		$this->weight = $shield->weight;
		$this->price = $shield->price;
		$this->modifiedTimes = 0;
		$this->durability = $shield->durability;
	}
	
	public function MouseLeftClickAttack(){
		$this->shield->pushing();
	}
	
	public function MouseRightClickAttack(){
		if($this->shield->getIsCovered()){
			$this->shield->remove();
		}else{
			$this->shield->cover();
		}
	}
}

//外部调用
class Client
{
	private $weapon;
	private $shield;
	private $adapter;
	public function __construct(){
		$this->weapon = new Knife();
		$this->shield = new RectangleShield();
		$this->adapter = new ShieldAdapter($this->shield);
		
		$this->UseWeapon($this->weapon);
		
		$this->UseWeapon($this->adapter);
		$this->UseWeapon($this->adapter);//通过适配器把shield套壳成weapon
	}
	
	public function UseWeapon(IWeaponPrototype $weapon){
		echo 'Weapon use:'.PHP_EOL;
		$weapon->MouseLeftClickAttack();		
		$weapon->MouseRightClickAttack();
	}
	
}

$worker = new Client();
// Weapon use:
// Knife cutting
// Knife stabbing
// Weapon use:
// uable to pushing, need shield covered
// shield covering
// Weapon use:
// shield pushing
// shield removing