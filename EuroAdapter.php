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
// ITarget实现修改汇率 PriceCalculator继承保证原结构可运行
// 两个类 通过Adapter用同一份参数