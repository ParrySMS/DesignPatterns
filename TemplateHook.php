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
    public function templateMethod($purchased, $hasDiscount,$hasShipping)
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
        $this->total = (1 + RADIO_TAX_JD) * $this->purchased;
		echo "after tax: $this->total" . PHP_EOL;
    }
	
	protected function addDiscountHook()
    {
        if (discount_hook) {
			$this->total = intval((1 - RADIO_DISCOUNT_OFF)*$this->total);
			echo "after discount: $this->total" . PHP_EOL;
        }
    }

    protected function addShippingHook()
    {
        if (shipping_hook) {
            $this->total += SHIPPING_FEE;
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
	const DEFAULT_PRODUCTSdefault_products = [
		'Dell-laptop' => 6799,
		'IPad2019' => 7859,
		'IPhone-XR' => 5100
	];
    private $product;
	private $shop;
	private $cost;
	private $hasDiscount; 
	private $hasShipping; 
	
    public function __construct(IShopHook $shop,$product = null)
    {
		
		$this->setProduct($product);
		$this->setCost();
		$this->shop = $shop;
		$this->hasDiscount = ($this->cost > 999);
		$this->hasShipping = ($this->cost < 99); 
		$this->shop->templateMethod($this->cost,$this->hasDiscount,$this->hasShipping);
    }
	
	private function setProduct($product)
	{
		if($product === null){
			$product = $this->default_products
		}	
	}	
	
}


