# Learning PHP Design Patterns (O'Reilly)

## PART1 基础

- 单一职责

- 保证调试中报告错误
```php
  ini_set("display_errors",1);
  ERROR_REPORTING(E_ALL);
```

- 使用`Trait`特性实现多重继承

- 接口不能包含变量，但可以有常量

```php
  interface IConnectInfo
  {
      const HOST = "localhost";
      const UNAME = "phpworker";
      public function testConnect();
  }
  
  class ConSQL implements IConnectInfo
  {
      private $host = IConnectInfo::HOST;
      private $uname = IConnectInfo::UNAME;
      public function testConnect($charset = "utf-8"){
          //code
      }
  }
```

- 使用接口做类型约束实现宽松绑定

```php
public function impltFunc(IConnectInfo $connectInfo){
      $connectInfo->otherfunc();//子类自己拓展的方法
  }
```

- 按照接口编程，解耦实现
- 对象不要一直继承拓展，应当使用组合
- UML关系
  - 相识:：包含引用
  - 聚合：多个对象组合作用完成
  - 继承和实现
  - 创建
  
## PART2 创建型设计模式

- 隐藏实例的创建过程，对象不应当与创建对象的过程紧密绑定

- 实例化对象的子类可能变化

### 工厂方法模式 Factory Method

- 工厂接口和产品接口的实现

```php
//产品接口
interface IProduct
{
    public function getProperties();
}

class Text implements IProduct
{
    private $msg;
    public function getProperties(){
        $this->msg="an example text";
        return $this->msg;
    }
}

class Graph implements IProduct
{
    private $msgForGraph;
    public function getProperties(){
        $this->mfg="an example graph";
        return $this->msgForGraph;
    }
}

//工厂接口
abstract class Creator
{
    protected abstract function factoryMethod();
    public function startFactory(){
        $msgForGraph = $this->factoryMethod();
        return $msgForGraph;
    }
}

//两个生产对应产品的特定工厂
class TextFactory extends Creator
{
    protected function factoryMethod(){
        $product = new Text();
        return $product->getProperties();//IProduct接口定义的方法
    }
}

class GraphicFactory extends Creator
{
    protected function factoryMethod(){
        $product = new Graph();
        return $product->getProperties();
    }
}

//调用类
class Client
{
    private $graphicObjs;
    private $textObjs;
    public function __construct(){
        //客户需要指定具体的工厂里来生产对应的产品
        $this->graphicObjs = new GraphicFactory();
        $this->textObjs = new TextFactory();
        $this->GraphTextStartFactory();//解耦了具体的对象实例化过程
    }
    private function GraphTextStartFactory(){
        echo $this->graphicObjs->startFactory();
        echo '<br/>';
        echo $this->textObjs->startFactory();
        echo '<br/>';
    }
}

//执行
$worker = new Client();
```



- 调整产品：当需求变动时，只需要修改具体的产品实现类 `Text` ,`Graph` 即可，因为请求只依赖接口，不依赖具体的产品实现。



```PHP
class Graph implements IProduct
{
    private $msgForGraph;
    private $imgStyle;
    public function __construct(){
    	$padding = 'padding: 10px 10px 10px 0px';
		$src = 'xxxx.png';
		$align = 'left';
		$width = '256';
		$height = '274';
		$this->imgStyle = compact('padding', 'src', 'align', 'width', 'height');
    }
    public function getProperties(){	
        $this->msgForGraph .= "<img style='{$this->imgStyle['padding']}' ";
        $this->msgForGraph .= "src='{$this->imgStyle['src']}' "; 
        $this->msgForGraph .= "align='{$this->imgStyle['align']}' "; 
        $this->msgForGraph .= "width='{$this->imgStyle['width']}' "; 
        $this->msgForGraph .= "height='{$this->imgStyle['height']}' "; 
        $this->msgForGraph .= '/>';
        return $this->msgForGraph;
    }
}
```



- 使用参数，一个工厂对应多个产品，调整原来一对一的特定工厂-产品关系

  

```php
<?php
//产品接口
interface IProduct
{
    public function getProperties();
}
//产品实现接口
class ChinaMap implements IProduct
{
    private $msgForGraph;
    public function __construct(){
    	$padding = 'padding: 10px 10px 10px 0px';
		$src = 'xxxx.png';
		$align = 'left';
		$width = '256';
		$height = '274';
		$this->imgStyle = compact('padding', 'src', 'align', 'width', 'height');
    }
    
    //两个产品内容放在一起实现
    public function getProperties(){      
        $this->msgForGraph .= "<img style='{$this->imgStyle['padding']}' ";
        $this->msgForGraph .= "src='{$this->imgStyle['src']}' "; 
        $this->msgForGraph .= "align='{$this->imgStyle['align']}' "; 
        $this->msgForGraph .= "width='{$this->imgStyle['width']}' "; 
        $this->msgForGraph .= "height='{$this->imgStyle['height']}' "; 
        $this->msgForGraph .= '/>';
        
        $this->msgForGraph .= '<header> CHINA-MAP </header>';
        $this->msgForGraph .='<p> Introduction of China </p>';

        return $this->msgForGraph;
    }
}

//带参数的工厂接口
abstract class Creator
{
    protected abstract function factoryMethod(IProduct $product);
    public function startFactory(IProduct $productNow){
        $chinaMapProduct = $productNow;
        //do sth with params
        $msgForGraph = $this->factoryMethod($chinaMapProduct);
        return $msgForGraph;
    }
}

//生产图像+文本产品的工厂
class ChinaMapFactory extends Creator
{
	private $countryMap;
    public function factoryMethod(IProduct $product){
        $this->countryMap = $product;
        return $this->countryMap->getProperties();
    }
}

//带参数的客户类
class Client
{
    private $chinaMapFactory;
    public function __construct(){
        $this->chinaMapFactory = new ChinaMapFactory();
        echo $this->chinaMapFactory->factoryMethod(new ChinaMap());
        //工厂通过参数 即new具体产品 实现产品的选择
        //$this->chinaMapFactory->factoryMethod(new OtherMap());
        
    }
}

//执行
$worker = new Client();
```



- 资源可以放在外部文件，通过文件读写获取，同理只需要更新产品即可



```php
class RussiaMap implements IProduct
{
	const FILE_MSG_TEXT_PATH = 'xxxx/RussiaMap.txt';
	const FILE_GRAPH_PATH = 'xxxx/RussiaMap.png';
	private $msgForGraph;
	private $msgText;
	private $formatHelper;
	public function __construct(){
	    //一个实现好封装格式方法的辅助类 能够输出对应的html内容
		$this->formatHelper = new FormatHelper();
		$this->msgText = file_get_contents(FILE_MSG_TEXT_PATH);
	}
	
	public function getProperties(){
		$this->msgForGraph = $this->formatHelper->addTop();
		$this->msgForGraph .= $this->formatHelper->addImg(FILE_GRAPH_PATH);
		$this->msgForGraph .= $this->formatHelper->addText($this->msgText);
		$this->msgForGraph .= $this->formatHelper->closeUp();
		return $this->msgForGraph;
	}
}
```



### 原型设计模式 Prototype

- 新对象通过复制原型实例化的具体类来创建，目的是减少实例化对象的开销
- PHP有内置的 `__clone()` 方法，这个方法不能直接调用，具体用法如下：
`$another = clone $someInstance`
- 克隆会替对应的类实例化另一个实例，不会启动构造函数
- 建议：设计模式和单元测试中的构造函数，不应该做具体的工作
- 用于批量产生对象，一次类实例化进行多次克隆。
- 动态产生实例对象或clone一个对象

```php
$className = "MyClass";
$myObj = new $className($params);
//等价于 $myObj = new MyClass($params);
```

- PHP 可以使用原型模式创建一个具体类的实例，然后利用数据库相关数据克隆其余实例，返回一个对象数组出去。

- 使用原型，批量生成若干个机器人玩家，玩家有两类武器  [RobotsWithWeapons.php](https://github.com/ParrySMS/DesignPatterns/blob/master/RobotsWithWeapons.php)

```php
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

    public function __construct()
    {
        $this->color = "black";
        $this->weight = 3.15;
        $this->price = 2300;
        $this->modifiedTimes = 0;
        $this->durability = 100;
        echo 'NOTE:Firearm __construct' . PHP_EOL;
    }

    private function randFloat($min, $max)
    {
        return rand() / getrandmax() * ($max - $min) + $min;
    }

    //克隆的同时调整一个随机浮动的耐久度
    function __clone()
    {
        $this->durability *= $this->randFloat(0.85, 2);
        $this->durability = intval($this->durability);
        echo 'NOTE:Firearm __clone: ';
        echo 'new durability:' . $this->durability . PHP_EOL;

    }
}

class Knife extends IWeaponPrototype
{
    const EQUIPED_BUTTON = 3;

    public function __construct()
    {
        $this->color = "grey";
        $this->weight = 0.75;
        $this->price = 850;
        $this->modifiedTimes = 0;
        $this->durability = 100;
        echo 'NOTE:Knife __construct' . PHP_EOL;
    }

    function __clone()
    {
        echo 'NOTE:Knife __clone' . PHP_EOL;
    }
}

//玩家原型以及实现gi
abstract class IPlayerPrototype
{
    protected $weaponEquipedOnButton1;
    protected $weaponEquipedOnButton3;
    protected $HP;
    protected $name;

    abstract public function setWeaponEquipedOnButton1(IWeaponPrototype $weaponOnBtn1);

    abstract public function setWeaponEquipedOnButton3(IWeaponPrototype $weaponOnBtn3);

    abstract public function setName($name);

    abstract function __clone();
}

class Robot extends IPlayerPrototype
{
    public function __construct(IWeaponPrototype $weaponOnBtn1, IWeaponPrototype $weaponOnBtn3)
    {
        $this->weaponEquipedOnButton1 = $weaponOnBtn1;
        $this->weaponEquipedOnButton3 = $weaponOnBtn3;
        $this->HP = 100;
        echo 'NOTE:Robot __construct' . PHP_EOL;
    }

    public function setWeaponEquipedOnButton1(IWeaponPrototype $weaponOnBtn1)
    {
        $this->weaponEquipedOnButton1 = $weaponOnBtn1;
    }

    public function setWeaponEquipedOnButton3(IWeaponPrototype $weaponOnBtn3)
    {
        $this->weaponEquipedOnButton3 = $weaponOnBtn3;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    function __clone()
    {
        echo 'NOTE:Robot __clone: ' . PHP_EOL;
    }
}

//创建若干机器人
class Client
{
    private $robot;
    private $firearm;
    private $knife;

    public function __construct()
    {
        $this->firearm = new Firearm();
        $this->knife = new Knife();
        $this->robot = new Robot($this->firearm, $this->knife);
    }

    public function createRobot($robotNum = 3)
    {
        $robots = [];
        for ($robotID = 0; $robotID < $robotNum; $robotID++) {
            $cloneFirearm = clone $this->firearm;
            $robotName = 'robotPlayer' . $robotID;

            $cloneRobot = clone $this->robot;
            $this->setRobot($cloneRobot, $robotName, $cloneFirearm);
            $robots[] = $cloneRobot;
        }
        return $robots;
    }

    private function setRobot(IPlayerPrototype $robot, $name, IWeaponPrototype $weaponOnBtn1)
    {
        $robot->setName($name);
        $robot->setWeaponEquipedOnButton1($weaponOnBtn1);
    }

}

//具体调用
$robotWorker = new Client();
$robots = $robotWorker->createRobot();
print_r($robots);
```

-------------------------

## PART3 结构型设计模式

- 创建新结构而不破坏原有结构

### 使用继承的类适配器模式

- 货币转化器：假设增加新的币种  [EuroAdapter.php](https://github.com/ParrySMS/DesignPatterns/blob/master/EuroAdapter.php)

![](https://raw.githubusercontent.com/ParrySMS/DesignPatterns/master/assets/AdapterExtend.jpg)

```php
<?php

//总价计算器
class PriceCalculator
{
    private $total_price;
    private $product_price;
    private $service_price;
    public $exchange_rate = 1;
    public $currency_name = "RMB";

    public function requestCalculator($product_price, $service_price)
    {
        $this->product_price = $product_price;
        $this->service_price = $service_price;
        $this->total_price = $product_price + $service_price;
        $this->total_price *= $this->exchange_rate;
        return $this->total_price;
    }
}

//欧区计算器 加税8%
class EuroCalculator
{
    private $total_price;
    private $product_price;
    private $service_price;
    private $tax_rate = 0.08;
    public $exchange_rate = 1;
    public $currency_name = "EUR";

    public function requestCalculator($product_price, $service_price)
    {
        $this->product_price = $product_price;
        $this->service_price = $service_price;
        $this->total_price = $product_price + $service_price;
        $this->total_price *= $this->exchange_rate;
        $this->total_price *= (1 + $this->tax_rate);
        return $this->total_price;
    }
}

//一个类适配器接口
interface ITarget
{
    public function rateRequester();//实现修改汇率
}

//适配器 一边继承价格计算器 另一边实现转化
class EuroAdapter extends EuroCalculator implements ITarget
{
    public $rate = 0.13;

    public function __construct()
    {
        $this->rateRequester();
    }

    public function rateRequester()
    {
        $this->exchange_rate = $this->rate;
        return $this->exchange_rate;
    }
}

class Client
{
    public $product_price;
    public $service_price;

    public function __construct($product_price, $service_price)
    {
        $this->product_price = $product_price;
        $this->service_price = $service_price;
        $this->showLocalRequestPrice(new PriceCalculator());
        $this->showOtherRequestPrice(new EuroAdapter());
    }

    public function showLocalRequestPrice(PriceCalculator $request)
    {
        echo 'showLocal--' . $request->currency_name . ' : ';
        echo $request->requestCalculator($this->product_price, $this->service_price);
        echo PHP_EOL;
    }

    //ITarget可更换为其他的实现
    public function showOtherRequestPrice(ITarget $other_request)
    {
        echo 'showOther--' . $other_request->currency_name . ' : ';
        echo $other_request->requestCalculator($this->product_price, $this->service_price);
        echo PHP_EOL;
    }
}

//调用
$product_price = 2199.00;
$service_price = 200.00;
$worker = new Client($product_price, $service_price);

//showLocal--RMB : 2399
//showOther--EUR : 336.8196

//当需要新的货币时，只需要实现适配器 class xxxAdapter extends PriceCalculator implements ITarget{}
// ITarget实现修改汇率 PriceCalculator继承保证原结构可运行
// 两个类 通过Adapter用同一份参数
```

### 使用组合的对象适配器模式

- [WeaponAdapter.php](https://github.com/ParrySMS/DesignPatterns/blob/master/WeaponAdapter.php)

![](https://raw.githubusercontent.com/ParrySMS/DesignPatterns/master/assets/AdapterOverride.jpg)


```php
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

    public function __construct()
    {
        $this->color = "grey";
        $this->weight = 0.75;
        $this->price = 850;
        $this->modifiedTimes = 0;
        $this->durability = 100;
    }

    public function MouseLeftClickAttack()
    {
        echo 'Knife cutting' . PHP_EOL;
    }

    public function MouseRightClickAttack()
    {
        echo 'Knife stabbing' . PHP_EOL;
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

    public function __construct()
    {
        $this->color = "black";
        $this->weight = 5.95;
        $this->price = 1050;
        $this->durability = 1000;
        $this->isCovered = false;
    }

    public function getIsCovered()
    {
        return $this->isCovered;
    }

    public function pushing()
    {
        if ($this->isCovered) {
            echo 'shield pushing' . PHP_EOL;
        } else {
            echo 'uable to pushing, need shield covered' . PHP_EOL;
        }
    }

    public function cover()
    {
        $this->isCovered = true;
        echo 'shield covering' . PHP_EOL;
    }

    public function remove()
    {
        $this->isCovered = false;
        echo 'shield removing' . PHP_EOL;
    }

    function __clone()
    {
        echo 'NOTE:RectangleShield __clone' . PHP_EOL;
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

    public function __construct(IShieldPrototype $shield)
    {
        $this->shield = $shield;
        $this->color = $shield->color;
        $this->weight = $shield->weight;
        $this->price = $shield->price;
        $this->modifiedTimes = 0;
        $this->durability = $shield->durability;
    }

    public function MouseLeftClickAttack()
    {
        $this->shield->pushing();
    }

    public function MouseRightClickAttack()
    {
        if ($this->shield->getIsCovered()) {
            $this->shield->remove();
        } else {
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

    public function __construct()
    {
        $this->weapon = new Knife();
        $this->shield = new RectangleShield();
        $this->adapter = new ShieldAdapter($this->shield);

        $this->UseWeapon($this->weapon);

        $this->UseWeapon($this->adapter);
        $this->UseWeapon($this->adapter);//通过适配器把shield套壳成weapon
    }

    public function UseWeapon(IWeaponPrototype $weapon)
    {
        echo 'Weapon use:' . PHP_EOL;
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
```

- 两种适配器模式 Adapter 小结: 用来处理不兼容的接口

![](https://raw.githubusercontent.com/ParrySMS/DesignPatterns/master/assets/Adapter.jpg)



### 装饰器模式 Decorator 

- **俄罗斯套娃**来套对象加东西
- 具体组件Component实现IComponent接口。
- 装饰器(Decorator)可以包装一个具体组件实例对象(Component)。
- 装饰器相当于具体装饰的接口，装饰器继承IComponent但不实现，只用于维护IComponent引用在具体装饰中实现。
- 具体装饰继承装饰器Decorator，构造函数里接收一个IComponent，用来实现组件的新装饰功能。
- 适配器和装饰器都可以称为 `包装器 Wrapper` 
- use abstract class but not interface for these elements in common
- some regular function not changed is able to implement in Decorator
- save the `$weapon` instance when constructing and then add more features. Most features using var from `$this->weapon` 

- [WeaponDecorator.php](https://github.com/ParrySMS/DesignPatterns/blob/master/WeaponDecorator.php)

```php
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
    public function setColor($color){
        $this->color =  $color;
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
		echo 'MainWeapon original getFeatures()'. PHP_EOL;
        $this->getFeatures();
		echo 'MainWeapon:__construct end'. PHP_EOL;
    }
	
    public function setColor($color = 'black'){
        $this->color =  $color;
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
	
	public function getFeatures(){		
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

    public function setColor($color = 'silver'){
        $this->color =  $color;
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
	
	public function getFeatures(){		
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
	
	public function setFireBuffBullet($buffBulletNum){
		$this->fireBuffBullet = $buffBulletNum;
	}

    public function MouseLeftClickAttack()
    {
        if ($this->fireBuffBullet > 0) {
            echo 'shoot a fire-bullet, damage is ';
			echo $this::DAMAGE_RATIO_FIRE_BUFF_SHOOT * $this->weapon->damage . PHP_EOL;
            echo 'Gunshots could be heard as ' .$this->weapon->sound . PHP_EOL;
			$this->fireBuffBullet -= $this::BULLET_FIRE_BUFF_SHOOT_COST;
        } else {
			$this->weapon->MouseLeftClickAttack();
		} 
	}
	
	public function MouseRightClickAttack()
    {
		$this->weapon->MouseRightClickAttack();
		
        if ($this->fireBuffBullet >  $this::BULLET_FIRE_BUFF_SKILL_COST) {
			echo 'use fire-buff skill, damage is ';  
			echo $this::DAMAGE_RATIO_FIRE_BUFF_SKILL * $this->weapon->damage . PHP_EOL;
			$this->fireBuffBullet -= $this::BULLET_FIRE_BUFF_SKILL_COST;
		}else {
            echo 'not enough fireBuffBullet to use fire-buff skill' . PHP_EOL;
        }
    }
	
	public function getFeatures(){		
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
		if($this->weapon->bullet == 0){
			$this->weapon->bullet += $this->reloadBulletNum;
		}
		
        $this->weapon->MouseRightClickAttack();
    }
	
	public function getFeatures(){		
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
```


## PART2 行为型设计模式

- 考虑对象与类之间的通信，互相合作来完成任务

### 模板方法模式  Template Method

- 使用抽象类中的一个具体方法，确定其他抽象方法的执行顺序。（类似Controllor）
- 具体实现交给具体类
- 适用于已经明确具体步骤，而步骤可抽象出多种实现的情况
- 使用模板方法可以控制子类拓展（钩子操作）
- 子类可以拓展或重新实现算法的可变部分，但是不能改变模板方法的控制流。
- 好莱坞原则（Hollywood Principle）: 反向控制结构概念，父类调用子类的操作，而子类不掉用父类。尽管实例化了一个具体类，但是调用的是父类的一个具体方法。这个具体方法里，父类会去调用子类的一些具体实现。
- 幼儿园原则（Kindergarden Principle）: 父类建立顺序，子类按照各自实现完成操作，但不能改变控制流。
- 模板方法与工厂方式的结合 [TemplateMethodFactory.php](https://github.com/ParrySMS/DesignPatterns/blob/master/TemplateMethodFactory.php)

![1566196251149](https://raw.githubusercontent.com/ParrySMS/DesignPatterns/master/assets/TemplateMethodFactory.png)

```php
<?php
require "./Factory.php";
/**
 * need to comments out the line with
 *    //$worker = new Client();
 * in Factory.php
 **/
// TMBase implement the templateMethod()
abstract class TMBase
{
    protected $msgForGraph;
    protected $msgForTM;

    public function templateMethod()
    {
        echo 'do sth 1:'. PHP_EOL;;
        $this->addCountryMap();
        echo 'do sth 2:'. PHP_EOL;;
        $this->addMsg();
    }

    //implemented by other class
    protected abstract function addCountryMap();

    protected abstract function addMsg();
}

class TMFactory extends TMBase
{
    protected function addCountryMap()
    {
        //Factory to produce <img /> and text
        $chinaMapFactory = new ChinaMapFactory();
        echo $chinaMapFactory->factoryMethod(new ChinaMap());
        echo PHP_EOL;
    }

    protected function addMsg()
    {
        echo 'able to add other Factory to use [ Factory->factoryMethod(new Product()) ]' . PHP_EOL;
    }

}


class TMClient
{
    public function __construct()
    {
        $TMFactory = new TMFactory();
        // the order of execution is defined in templateMethod()
        // just excute it
        $TMFactory->templateMethod();
    }
}

$clinet = new TMClient();
//do sth 1:
//<img style='padding: 10px 10px 10px 0px' src='xxxx.png' align='left' width='256' height='274' /><header> CHINA-MAP </header><p> Introduction of China </p>
//do sth 2:
//able to add other Factory to use [ Factory->factoryMethod(new Product()) ]
```


- 模板方法设计模式中的钩子
	- 对于一些特殊情况可以不执行某些步骤或执行其他内容
	- 将一个方法作为模板方法的一部分，但是用来处理例外情况（可能执行，也可能不执行）
	- 尽管子类可以改变钩子的行为，但仍要遵循定义的模板方法顺序
	- 某个钩子方法, 用一些条件来控制具体执行内容。
	- 在所有的钩子操作中，必须要警告控制流，说明当前状态有不同情况发生，不是执行默认的控制流。

