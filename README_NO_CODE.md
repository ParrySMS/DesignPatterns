# Learning PHP Design Patterns (O'Reilly)

## 简洁版本：[README_NO_CODE.md](https://github.com/ParrySMS/DesignPatterns/blob/master/README_NO_CODE.md)

长代码块放置外链,文档经 [rmCodeInMd.php](https://github.com/ParrySMS/DesignPatterns/blob/master/rmCodeInMd.php) 处理生成


- [PART1 基础](#part1-基础)   
- [PART2 创建型设计模式](#part2-创建型设计模式)
    - [工厂方法模式 Factory Method](#工厂方法模式-factory-method)
    - [原型设计模式 Prototype](#原型设计模式-prototype)
- [PART3 结构型设计模式](#part3-结构型设计模式)
    - [使用继承的类适配器模式](#使用继承的类适配器模式)
    - [使用组合的对象适配器模式](#使用组合的对象适配器模式)
    - [装饰器模式 Decorator](#装饰器模式-decorator)
- [PART4 行为型设计模式](#part4-行为型设计模式)
    - [模板方法模式  Template Method](#模板方法模式--template-method)
    - [状态设计模式 State](#状态设计模式-state)
- [PART5 MySQL相关设计模式](#part5-mysql相关设计模式)
    - [代理 (Proxy)](#代理-proxy)
    - [职责链模式](#职责链模式)

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




- 调整产品：当需求变动时，只需要修改具体的产品实现类 `Text` ,`Graph` 即可，因为请求只依赖接口，不依赖具体的产品实现。






- 使用参数，一个工厂对应多个产品，调整原来一对一的特定工厂-产品关系

  




- 资源可以放在外部文件，通过文件读写获取，同理只需要更新产品即可




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


-------------------------

## PART3 结构型设计模式

- 创建新结构而不破坏原有结构

### 使用继承的类适配器模式

- 货币转化器：假设增加新的币种  [EuroAdapter.php](https://github.com/ParrySMS/DesignPatterns/blob/master/EuroAdapter.php)

![](https://raw.githubusercontent.com/ParrySMS/DesignPatterns/master/assets/AdapterExtend.jpg)


### 使用组合的对象适配器模式

- [WeaponAdapter.php](https://github.com/ParrySMS/DesignPatterns/blob/master/WeaponAdapter.php)

![](https://raw.githubusercontent.com/ParrySMS/DesignPatterns/master/assets/AdapterOverride.jpg)



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



- 模板方法设计模式中的钩子
	- 对于一些特殊情况可以不执行某些步骤或执行其他内容
	- 将一个方法作为模板方法的一部分，但是用来处理例外情况（可能执行，也可能不执行）
	- 尽管子类可以改变钩子的行为，但仍要遵循定义的模板方法顺序
	- 某个钩子方法, 用一些条件来控制具体执行内容。
	- 在所有的钩子操作中，必须要警告控制流，说明当前状态有不同情况发生，不是执行默认的控制流。
	- 一个购物清单的例子 [TemplateHook.php](https://github.com/ParrySMS/DesignPatterns/blob/master/TemplateHook.php)
	


### 状态设计模式 State 

- 常见于游戏，因为游戏中的对象频繁改变状态

- 用状态+触发器（state and triggers），取代复杂的大量条件语句

- 所有状态模式都需要一个参与者来跟踪对象所处的状态（当前状态）

- 系统需要知道可通过哪些迁移进入到其他状态（状态转移的下一步），下文例中使用 Context 类 (表示上下文情境)
	
- 可以使用状态转移图确认具体流程	

- 例1 灯泡状态 [BulbContextState.php](https://github.com/ParrySMS/DesignPatterns/blob/master/BulbContextState.php)


- 例2 游戏角色 [PlayerContextState.php](https://github.com/ParrySMS/DesignPatterns/blob/master/PlayerContextState.php)

```
[initial state] ---> wait 5 sec ---> [playing state]
[playing state] ---> check health, if 0 ---> [reviving state]
[reviving state] ---> wait 3 sec ---> [protected state]
[protected state] ---> wait 3 sec ---> [playing state]
```



- 最开始需要加载全部可能状态，运行之后只是做状态转移赋值。（状态类可能有冗余，但是状态转移会简化）

- 一个个状态单纯用作存储用，转换行为在各个状态里有定义，但是触发是用 Context 上下文情境里的方法触发的，所以调用逻辑在 Context 里。

- 可以根据情况再去分类抽象掉 IState 接口，不然如果 state状态实例 可能会有很多不实现的空方法（因为其他的状态不一定都下步可达）。

![contextState](https://raw.githubusercontent.com/ParrySMS/DesignPatterns/master/assets/contextState.png)


## PART5 MySQL相关设计模式

- 代理 (Proxy)

- 策略 (Strategy)

- 职责链 (Chain of Responsibility)

- 观察者 (Observer)

### 代理 (Proxy)

- 通用DB连接类和静态变量
	- 接口常量用来存连接配置
	- 尽量避免全局变量，因为可能会破坏封装，可以用静态取代
	- 作者认为，真正正确的单例模式就相当于全局变量
	- 书中 P216-218 代码有误，应当声明 `doConnect()` 为静态，修正代码如下：
	- DB连接类的静态方法和静态变量 [DBConnect.php](https://github.com/ParrySMS/DesignPatterns/blob/master/DBConnect.php)


- 代理模式：保护代理完成登录
    - 结构型设计模式
        - 远程代理 remote：两个不同的地址空间
        - 虚拟代理 virtual ：缓存真实主题
        - 保护代理 protection ：权限
        - 智能引用 smart reference ：引用对象时完成额外的操作
    - 参与者：代理主题 proxy subject 以及 真实主题 real subject， 客户通过代理来操作

![proxy](https://raw.githubusercontent.com/ParrySMS/DesignPatterns/master/assets/proxy.jpg)


（跳跃章节）

### 职责链模式

- 避免请求者和接收者的耦合: 发送者不需要指定哪个对象来处理，对象也不需要知道哪个对象发送
