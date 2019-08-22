<?php

/**
 * Class IHook
 *  add tax and shipping, then show the cost
 */
abstract class IHook
{
    protected $purchased;
    protected $discount_hook;
    protected $shipping_hook;
    protected $total;

    // define the sequence
    public function templateMethod($purchased, $special_discount)
    {
        $this->purchased = $purchased;
        $this->discount_hook = $special_discount;
        $this->addTax();
        $this->addShippingHook();
        $this->displayCost();
    }

    protected abstract function addTax();

    protected abstract function addShippingHook();

    protected abstract function displayCost();
}

/**
 * class JingDongShop
 *  20% off if the price more than 999 RMB
 *  no shipping fee if price more than 99 RMB
 */
class JingDongShop extends IHook
{
    const RADIO_TAX_JD = 0.05;
    const SHIPPING_FEE = 9;

    protected function addTax()
    {
        $this->total = (1 + RADIO_TAX_JD) * $this->purchased;
    }

    protected function addShippingHook()
    {
        if (!discount_hook) {
            $this->total += SHIPPING_FEE;
        }
    }

    protected function displayCost()
    {
        echo "cost: $this->total" . PHP_EOL;
    }
}

class Client
{
    private $product;//todo: name=>price
    public function __construct()
    {

    }
}


