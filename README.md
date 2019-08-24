# Learning PHP Design Patterns (O'Reilly)

## 简洁版本：[README_NO_CODE.md](https://github.com/ParrySMS/DesignPatterns/blob/master/README_NO_CODE.md)

- 长代码块放置外链

- 由文档经 [rmCodeInMd.php](https://github.com/ParrySMS/DesignPatterns/blob/master/rmCodeInMd.php) 处理生成

## PART1 基础

- 单一职责

- 保证调试中报告错误
```
  ini_set("display_errors",1);
  ERROR_REPORTING(E_ALL);
```

- 使用`Trait`特性实现多重继承

- 接口不能包含变量，但可以有常量

```
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

```
public function impltFunc(IConnectInfo $connectInfo){
      $connectInfo->otherfunc();//子类自己拓展的方法
  }
```

- 按照接口编程，解耦实现

- 对象不要一直继承拓展，应当使用组合

- UML关系
  - 相识: 包含引用
  - 聚合: 多个对象组合作用完成
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

- 工厂模式示例代码: [Factory.php](https://github.com/ParrySMS/DesignPatterns/blob/master/Factory.php)

### 原型设计模式 Prototype

- 新对象通过复制原型实例化的具体类来创建，目的是减少实例化对象的开销

- PHP有内置的 `__clone()` 方法，这个方法不能直接调用，具体用法如下：
`$another = clone $someInstance`

- 克隆会替对应的类实例化另一个实例，不会启动构造函数

- 建议：设计模式和单元测试中的构造函数，不应该做具体的工作

- 用于批量产生对象，一次类实例化进行多次克隆。

- 动态产生实例对象或clone一个对象

```
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


## PART4 行为型设计模式

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

![TemplateMethodFactory](https://raw.githubusercontent.com/ParrySMS/DesignPatterns/master/assets/TemplateMethodFactory.png)

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
	- 一个购物清单的例子 [TemplateHook.php](https://github.com/ParrySMS/DesignPatterns/blob/master/TemplateHook.php)
	
```php
<?php
/**
 * Class IShopHook
 *  add tax and shipping, then show the cost
 */
abstract class IShopHook
{
    protected $purchased;
    protected $discount_hook;
    protected $shipping_hook;
    protected $total;

    // define the sequence
    public function templateMethod($purchased, $hasDiscount, $hasShipping)
    {
        $this->purchased = $purchased;
        $this->discount_hook = $hasDiscount;
        $this->shipping_hook = $hasShipping;
        $this->addTax();
        $this->addDiscountHook();
        $this->addShippingHook();
        $this->displayCost();
    }
    protected abstract function addTax();
    protected abstract function addDiscountHook();
    protected abstract function addShippingHook();
    protected abstract function displayCost();
}

/**
 * class JingDongShop
 */
class JingDongShop extends IShopHook
{
    const RADIO_TAX_JD = 0.05;
    const RADIO_DISCOUNT_OFF = 0.20;
    const SHIPPING_FEE = 9;

    protected function addTax()
    {
        $this->total = (1 + $this::RADIO_TAX_JD) * $this->purchased;
        echo "after tax: $this->total" . PHP_EOL;
    }

    protected function addDiscountHook()
    {
        if ($this->discount_hook) {
            $this->total = intval((1 - $this::RADIO_DISCOUNT_OFF) * $this->total);
            echo "after discount: $this->total" . PHP_EOL;
        }
    }

    protected function addShippingHook()
    {
        if ($this->shipping_hook) {
            $this->total += $this::SHIPPING_FEE;
            echo "after shipping: $this->total" . PHP_EOL;
        }
    }

    protected function displayCost()
    {
        echo "final: $this->total" . PHP_EOL;
    }
}

/**
 *  20% off if the price more than 999 RMB
 *  need shipping fee if price less than 99 RMB
 */
class Client
{
    const DEFAULT_PRODUCTS = ['Dell-laptop' => 6799, 'IPad2019' => 7859, 'IPhone-XR' => 5100];
    private $product;
    private $shop;
    private $cost;
    private $hasDiscount;
    private $hasShipping;

    public function __construct(IShopHook $shop, $product = null)
    {

        $this->setProduct($product);
        $this->setCost();
        $this->shop = $shop;
        $this->hasDiscount = ($this->cost > 999);
        $this->hasShipping = ($this->cost < 99);
        $this->shop->templateMethod($this->cost, $this->hasDiscount, $this->hasShipping);
    }

    private function setProduct($product)
    {
        if ($product === null) {
            $this->product = $this::DEFAULT_PRODUCTS;
        } else {
            $this->product = $product;
        }
    }

    private function setCost()
    {
        echo 'Products :'.PHP_EOL;
        foreach ($this->product as $name =>$price) {
            echo "$name : $price RMB".PHP_EOL;
            $this->cost += $price;
        }
        echo PHP_EOL;
    }
}

$worker = new Client(new JingDongShop());
//Products :
//Dell-laptop : 6799 RMB
//IPad2019 : 7859 RMB
//IPhone-XR : 5100 RMB
//
//after tax: 20745.9
//after discount: 16596
//final: 16596
```


### 状态设计模式 State 

- 常见于游戏，因为游戏中的对象频繁改变状态

- 用状态+触发器（state and triggers），取代复杂的大量条件语句

- 所有状态模式都需要一个参与者来跟踪对象所处的状态（当前状态）

- 系统需要知道可通过哪些迁移进入到其他状态（状态转移的下一步），下文例中使用 Context 类 (表示上下文情境)
	
- 可以使用状态转移图确认具体流程	

- 例1 灯泡状态 [BulbContextState.php](https://github.com/ParrySMS/DesignPatterns/blob/master/BulbContextState.php)

```php
<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2019-8-23
 * Time: 18:40
 */


/**
 * Interface IState
 * show all possible state triggers
 */
interface IState
{
    public function turnOn();
    public function turnOff();
    public function turnBrightest();
    public function convertFlash();
}

/**
 * define all state implement class
 */
class OffState implements IState
{
    private $bulb;
    public function __construct(BulbContext $bulb)
    {
        $this->bulb = $bulb;
    }

    //// Only this trigger available when a bulb in OffState
    public function turnOn()
    {
        echo 'light on' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getOnState());
    }
    public function turnBrightest(){
        echo 'please swirch the light on first' . PHP_EOL;
    }

    //// null implement
    public function turnOff(){}
    public function convertFlash(){}
}

class OnState implements IState
{
    private $bulb;
    public function __construct(BulbContext $bulb)
    {
        $this->bulb = $bulb;
    }

    //// null implement
    public function turnOn(){}

    public function turnOff()
    {
        echo 'light off' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getOffState());
    }

    public function turnBrightest()
    {
        echo 'light to brightest' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getBrightestState());
    }

    public function convertFlash()
    {
        echo 'light flashing' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getFlashState());
    }
}

class BrightestState implements IState
{
    private $bulb;
    public function __construct(BulbContext $bulb)
    {
        $this->bulb = $bulb;
    }

    public function turnOn(){}

    public function turnOff()
    {
        echo 'light from brightest to dark .... light off' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getOffState());
    }

    public function turnBrightest()
    {
        echo 'light allready brightest' . PHP_EOL;
    }

    public function convertFlash()
    {
        echo 'light flashing with brightest light' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getFlashState());
    }
}

class FlashState implements IState
{
    private $bulb;
    public function __construct(BulbContext $bulb)
    {
        $this->bulb = $bulb;
    }

    public function turnOn(){}

    public function turnOff()
    {
        echo 'light from flashing to dark .... light off' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getOffState());
    }

    public function turnBrightest()
    {
        echo 'light seem brighter gradually .... brightest' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getBrightestState());
    }

    public function convertFlash()
    {
        echo 'no flashing' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getOnState());
    }
}

/**
 * Class Bulb
 * Bulb is able to switch itself to diff state
 */
class BulbContext
{
    public $luminance;

    private $on_state;
    private $off_state;
    private $brightest_state;
    private $flash_state;

    private $current_state;

    public function __construct()
    {
        // init all state
        $this->on_state = new OnState($this);
        $this->off_state = new OffState($this);
        $this->brightest_state = new BrightestState($this);
        $this->flash_state = new FlashState($this);
        // default state is offState
        $this->current_state = $this->off_state;
        $this->luminance = '0%';
        $this->getSateInfo();
    }

    // use trigger methods
    public function turnOn()
    {
        $this->current_state->turnOn();
        $this->luminance = '80%';
        $this->getSateInfo();
    }

    public function turnOff()
    {
        $this->current_state->turnOff();
        $this->luminance = '0%';
        $this->getSateInfo();
    }

    public function turnBrightest()
    {
        $this->current_state->turnBrightest();
        $this->luminance = '100%';
        $this->getSateInfo();
    }

    public function convertFlash()
    {
        $this->current_state->convertFlash();
        $this->getSateInfo();
    }

    public function getSateInfo()
    {
        echo "luminance:$this->luminance".PHP_EOL;
        echo 'currentState:'.get_class($this->current_state).PHP_EOL;
    }

    //// Getter Setter
    public function setCurrentState(IState $current_state)
    {
        $this->current_state = $current_state;
    }


    public function getOnState()
    {
        return $this->on_state;
    }

    public function getOffState()
    {
        return $this->off_state;
    }

    public function getBrightestState()
    {
        return $this->brightest_state;
    }

    public function getFlashState()
    {
        return $this->flash_state;
    }

}

class Client {
    private $bulb;
    private $func_list;

    public function __construct(BulbContext $bulb, $rand_times = 0)
    {
        $this->bulb = $bulb;
        $this->func_list = ['turnOn','turnBrightest','convertFlash','turnOff'];
        if($rand_times === 0){
            $this->seqTurn();
        }else {
            while($rand_times--) {
                $this->randTurn();
            }
        }
    }

    private function randTurn(){
        $rand_op = intval($this->randInt(1,4));
        $func = $this->func_list[$rand_op-1];
        echo PHP_EOL."operation:$func".PHP_EOL;
        $this->bulb->$func();
    }

    private function seqTurn(){
        foreach ($this->func_list as $func){
            echo PHP_EOL."operation:$func".PHP_EOL;
            $this->bulb->$func();
        }
    }

    private function randInt($min, $max)
    {
        return round (rand() / getrandmax() * ($max - $min) + $min);
    }
}

$worker = new Client(new BulbContext());
//luminance:0%
//currentState:OffState
//
//operation:turnOn
//light on
//luminance:80%
//currentState:OnState
//
//operation:turnBrightest
//light to brightest
//luminance:100%
//currentState:BrightestState
//
//operation:convertFlash
//light flashing with brightest light
//luminance:100%
//currentState:FlashState
//
//operation:turnOff
//light from flashing to dark .... light off
//luminance:0%
//currentState:OffState
```

- 例2 游戏角色 [PlayerContextState.php](https://github.com/ParrySMS/DesignPatterns/blob/master/PlayerContextState.php)

```
[initial state] ---> wait 5 sec ---> [playing state]
[playing state] ---> check health, if 0 ---> [reviving state]
[reviving state] ---> wait 3 sec ---> [protected state]
[protected state] ---> wait 3 sec ---> [playing state]
```


```php
<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2019-8-24
 * Time: 10:16
 */
define('HEALTH_MAX', 100);
define('HEALTH_MIN', 0);
define('TIME_GAME_HOLDING', 15);
define('TIME_INIT', 1);
define('TIME_REVIVING', 1);
define('TIME_PROTECTED', 1);
define('TIME_PLAYING',1);
define('TIMES_RAND_DAMAGE',1); // P_dead = 5151/10201

abstract Class IGameState
{
    abstract public function turnPlaying();
    abstract public function turnReviving();
    abstract public function turnProtected();
}

class PlayerContext
{
    public $health;
    private $initial_state;
    private $playing_state;
    private $reviving_state;
    private $protected_state;
    private $current_state;

    public function __construct($health = HEALTH_MIN)
    {
        $this->initial_state = new InitialState($this);
        $this->playing_state = new PlayingState($this);
        $this->reviving_state = new RevivingState($this);
        $this->protected_state = new ProtectedState($this);

        $this->current_state = $this->initial_state;
        $this->health = $health;
    }

    //// some functional PlayerContext methods
    public function getTimeAndWait($sec)
    {
        echo date('H:i:s') . PHP_EOL . PHP_EOL;
        sleep($sec);
    }

    public function setRandDamage()
    {
        if($this->health>0) {
            $damage = $this->randInt(HEALTH_MIN, HEALTH_MAX);
            $this->health -= $damage;
            echo "Damage:$damage, HP:$this->health" . PHP_EOL;
        }
    }

    public function randInt($min, $max)
    {
        return round(rand() / getrandmax() * ($max - $min) + $min);
    }

    public function autoTurning($rand_damage_times = TIMES_RAND_DAMAGE)
    {
        $this->start();

        while ($rand_damage_times--){
            $this->setRandDamage();
        }

        if(0 > $this->health){
            $this->dead();
            $this->beenProtected();
        }
    }

    //// use state trigger methods
    public function start()
    {
        $this->current_state->turnPlaying();
    }

    public function dead()
    {
        $this->current_state->turnReviving();
    }

    public function beenProtected()
    {
        $this->current_state->turnProtected();
    }

    //// setter getter
    public function setCurrentState($current_state)
    {
        $this->current_state = $current_state;
    }

    public function getCurrentState()
    {
        return $this->current_state;
    }

    public function getInitialState()
    {
        return $this->initial_state;
    }

    public function getPlayingState()
    {
        return $this->playing_state;
    }

    public function getRevivingState()
    {
        return $this->reviving_state;
    }

    public function getProtectedState()
    {
        return $this->protected_state;
    }

}

class InitialState extends IGameState
{
    private $context;

    public function __construct(PlayerContext $context)
    {
        echo 'Game starting now' . PHP_EOL;
        $this->context = $context;
    }

    public function turnPlaying()
    {
        echo 'Storm the front. Ok, let\'s go! ' . PHP_EOL;
        $this->context->getTimeAndWait(TIME_INIT);
        $this->context->health = HEALTH_MAX;
        $this->context->setCurrentState($this->context->getPlayingState());
    }

    public function turnReviving(){}
    public function turnProtected(){}
}

class PlayingState extends IGameState
{
    private $context;

    private $msg = [
        'Stick together team. Hold this position.',
        'Cover me, Cover me, Cover me!',
        'You take the point. I cover you.',
        'Fall Back! Fall Back!',
        'Get in position and wait for my go!',
        'Storm the front! Go!',
        'Fire! Fire! Taking fire!'
        ];

    public function __construct(PlayerContext $context)
    {
        $this->context = $context;
    }

    public function turnPlaying(){
        $msg_index = intval($this->context->randInt(0,sizeof($this->msg)-1));
        echo 'Player :'.$this->msg[$msg_index].PHP_EOL;
        $this->context->getTimeAndWait(TIME_PLAYING);
    }

    public function turnReviving()
    {
        echo 'Taking fire, need assistance! I \'m down.' . PHP_EOL;
        $this->context->setCurrentState($this->context->getRevivingState());
    }

    public function turnProtected(){}
}

class RevivingState extends IGameState
{
    private $context;

    public function __construct(PlayerContext $context)
    {
        $this->context = $context;
    }

    public function turnPlaying(){}
    public function turnReviving(){}
    public function turnProtected(){
        echo 'Reviviing...' . PHP_EOL;
        $this->context->getTimeAndWait(TIME_REVIVING);
        $this->context->setCurrentState($this->context->getProtectedState());
    }
}

class ProtectedState extends IGameState
{
    private $context;

    public function __construct(PlayerContext $context)
    {
        $this->context = $context;
    }
    public function turnReviving(){}
    public function turnProtected(){}
    public function turnPlaying(){
        echo 'Player is invulnerable now.' . PHP_EOL;
        $this->context->getTimeAndWait(TIME_PROTECTED);
        echo 'Player is vulnerable now.' . PHP_EOL;
        $this->context->getTimeAndWait(0);
        $this->context->health = HEALTH_MAX;
        $this->context->setCurrentState($this->context->getPlayingState());
    }
}

class Client
{
    private $context;

    public function __construct(PlayerContext $context)
    {
        $this->context = $context;
        $this->startGame();
    }

    private function startGame($sec = TIME_GAME_HOLDING)
    {
        $this->context->start();
        $start = time();
        while (1) {
            $this->context->autoTurning();
            sleep(TIME_PLAYING);
            if(time()-$start > $sec){
                break;
            }
        }
        echo 'Game End'.PHP_EOL;
    }
}

$worker = new Client(new PlayerContext());
//Game starting now
//Storm the front. Ok, let's go!
//15:26:21
//
//Player :You take the point. I cover you.
//15:26:22
//
//Damage:81, HP:19
//Player :Get in position and wait for my go!
//15:26:24
//
//Damage:47, HP:-28
//Taking fire, need assistance! I 'm down.
//Reviviing...
//15:26:25
//
//Player is invulnerable now.
//15:26:27
//
//Player is vulnerable now.
//15:26:28
//
//Damage:70, HP:30
//Player :Stick together team. Hold this position.
//15:26:29
//
//Damage:91, HP:-61
//Taking fire, need assistance! I 'm down.
//Reviviing...
//15:26:30
//
//Player is invulnerable now.
//15:26:32
//
//Player is vulnerable now.
//15:26:33
//
//Damage:36, HP:64
//Player :Get in position and wait for my go!
//15:26:34
//
//Damage:85, HP:-21
//Taking fire, need assistance! I 'm down.
//Reviviing...
//15:26:35
//
//Player is invulnerable now.
//15:26:37
//
//Player is vulnerable now.
//15:26:38
//
//Damage:42, HP:58
//Game End
```

- 最开始需要加载全部可能状态，运行之后只是做状态转移赋值。（状态类可能有冗余，但是状态转移会简化）

- 一个个状态单纯用作存储用，转换行为在各个状态里有定义，但是触发是用 Context 上下文情境里的方法触发的，所以调用逻辑在 Context 里。

- 可以根据情况再去分类抽象掉 IState 接口，不然如果 state状态实例 可能会有很多不实现的空方法（因为其他的状态不一定都下步可达）。

![contextState](https://raw.githubusercontent.com/ParrySMS/DesignPatterns/master/assets/contextState.png)


### MySQL相关设计模式

