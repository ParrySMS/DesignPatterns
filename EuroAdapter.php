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
    public function requester();//实现请求其他货币价格
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
        //举例使用原本类和适配器类来输出
        $this->other_request = $other_request;
        $this->loacl_request = new PriceCalculator();
    }
    
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