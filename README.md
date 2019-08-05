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

### 工厂模式

- 工厂接口和产品接口的实现

```php
//产品接口
interface IProduct
{
    public function getProperties();
}

class Text implements IProduct
{
    private $mfg;
    public function getProperties(){
        $this->mfg="an example text";
        return $mfg;
    }
}

class Graph implements IProduct
{
    private $mfg;
    public function getProperties(){
        $this->mfg="an example graph";
        return $mfg;
    }
}

//工厂接口
abstract class Creator
{
    protected abstract function factoryMethod();
    public function startFactory(){
        $mfg = $this->factoryMethod();
        return $mfg;
    }
}

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
        $this->graphicObjs = new GraphicFactory();
        $this->textObjs = new TextFactory();
        $this->echoGraphText();//解耦了具体的对象实例化过程
    }
    private function echoGraphText(){
        echo $this->graphicObjs->startFactory();
        echo '<br/>';
        echo $this->textObjs->startFactory();
        echo '<br/>';
    }
}

//执行
$worker = new Client();
```

