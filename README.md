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
    	
        $this->msgForGraph = "<img style='{$imgStyle['padding']}'; 
        src='{$imgStyle['src']}' align='{$imgStyle['align']}'
        width='{$imgStyle['width']}' height='{$imgStyle['height']}'
        >" ;
        return $this->msgForGraph;
    }
}
```



- 使用参数，一个工厂对应多个产品，调整原来一对一的特定工厂-产品关系

  

```php
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
        $this->msgForGraph .= "<img 
        style='{$imgStyle['padding']}'; 
        src='{$imgStyle['src']}' 
        align='{$imgStyle['align']}'
        width='{$imgStyle['width']}' 
        height='{$imgStyle['height']}'
        >" ;
        
        $this->msgForGraph .= <<<CHINAMAP
		<header>CHINA-MAP</header> <p> Introduction of China </p>
CHINAMAP        

        return $this->mfg;
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
    protected function factoryMethod(IProduct $product){
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
	protected function randFloat($min,$max){
		return rand()/getrandmax()*($max-$min)+$min;
	}
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
	//克隆的同时调整一个随机浮动的耐久度
	function __clone(){
        $this->durability *= $this->randFloat(0.85,2);
		$this->durability = intval($this->durability);
		echo 'NOTE:Firearm __clone: ';
		echo 'new durability:'.$this->durability.PHP_EOL;
		
    }
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
	function __clone(){
		echo 'NOTE:Knife __clone'.PHP_EOL;
	}
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
        
    function __clone(){
		echo 'NOTE:Robot __clone: '.PHP_EOL;
	}
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
	
	public function createRobot($robotNum = 3){	  
		$robots = [];
		for($robotID = 0;$robotID < $robotNum;$robotID++){
    		$cloneFirearm = clone $this->firearm;
    		$robotName = 'robotPlayer'.$robotID;
    		
            $cloneRobot = clone $this->robot;
            $this->setRobot($cloneRobot,$robotName,$cloneFirearm);	
            $robots[] = $cloneRobot;
    	}
    	return $robots; 
	}
    
	private function setRobot(IPlayerPrototype $robot,$name,IWeaponPrototype $weaponOnBtn1){
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
    
    public function requestCalculator($product_price,$service_price){
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
    
    public function requestCalculator($product_price,$service_price){
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
    
    public function __construct(){  
        $this->rateRequester();
    }
    
    public function rateRequester(){
        $this->exchange_rate = $this->rate;
        return $this->exchange_rate;
    }
}

class Client
{
    public $product_price;
    public $service_price;
    
    public function __construct($product_price,$service_price){
        $this->product_price = $product_price;
        $this->service_price = $service_price;
        $this->showLocalRequestPrice(new PriceCalculator());  
        $this->showOtherRequestPrice(new EuroAdapter());
    }
    
    public function showLocalRequestPrice(PriceCalculator $request){
        echo 'showLocal--'.$request->currency_name.' : ';
        echo $request->requestCalculator($this->product_price, $this->service_price);
        echo PHP_EOL;
    }
    
    //ITarget可更换为其他的实现
    public function showOtherRequestPrice(ITarget $other_request){       
        echo 'showOther--'.$other_request->currency_name.' : ';
        echo $other_request->requestCalculator($this->product_price, $this->service_price);
        echo PHP_EOL;        
    }  
}

//调用
$product_price = 2199.00;
$service_price = 200.00;
$worker = new Client($product_price,$service_price);

//showLocal--RMB : 2399
//showOther--EUR : 336.8196

//当需要新的货币时，只需要实现适配器 class xxxAdapter extends PriceCalculator implements ITarget{}
// ITarget实现修改汇率 两个类 通过 xxxAdapter 实现继续用同一份参数 
```

### 使用组合的对象适配器模式


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
```

- 两种适配器模式 Adapter 小结

![](https://raw.githubusercontent.com/ParrySMS/DesignPatterns/master/assets/Adapter.jpg)



### 装饰器 
