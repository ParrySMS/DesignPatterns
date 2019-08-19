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
        $mapProduct = $productNow;
        //do sth with params
        $msgForGraph = $this->factoryMethod($mapProduct);
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
//$worker = new Client();