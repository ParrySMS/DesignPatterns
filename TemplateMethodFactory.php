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