# Learning PHP Design Patterns (O'Reilly)

## C1 面向对象

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

  
