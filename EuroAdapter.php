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

//一个类适配器接口
interface ITarget
{
    public function requester();//实现修改汇率
}

//适配器 一边继承价格计算器 另一边实现转化
class EuroAdapter extends PriceCalculator implements ITarget
{	
    public $euro_rate = 0.13;
    
    public function __construct(){  
        $this->currency_name = "EUR";
        $this->requester();
    }
    
    public function requester(){
        $this->exchange_rate = $this->euro_rate;
        return $this->exchange_rate;
    }
}

class Client
{
    private $loacl_request;
    private $other_request;   
    public function getLoaclRequest(){ return $this->loacl_request; }
    public function getOtherRequest(){ return $this->other_request; }
    public function __construct(ITarget $other_request){
        $this->other_request = $other_request;//保存一个实现 ITarget 修改汇率的新请求
        $this->loacl_request = new PriceCalculator();
    }
    
    //参数PriceCalculator约束 原结构可运行
    public function showRequestPrice(PriceCalculator $request){
        $product_price = 2199.00;
        $service_price = 200.00;
        echo $request->currency_name." : ";
        echo $request->requestCalculator($product_price, $service_price);
        echo PHP_EOL;        
    }
}

//调用
$worker = new Client(new EuroAdapter());
$worker->showRequestPrice($worker->getLoaclRequest());
$worker->showRequestPrice($worker->getOtherRequest());
//RMB : 2399
//EUR : 311.87

//当需要新的货币时，只需要实现适配器 class xxxAdapter extends PriceCalculator implements ITarget{}
// ITarget实现修改汇率 PriceCalculator继承保证原结构可运行