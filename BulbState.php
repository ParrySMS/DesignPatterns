<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2019-8-23
 * Time: 18:40
 */


/**
 * Interface IState
 * show all possible state triggers
 */
interface IState
{
    public function turnOn();
    public function turnOff();
    public function turnBrightest();
    public function convertFlash();
}

/**
 * define all state implement class
 */
class OffState implements IState
{
    private $bulb;
    public function __construct(Bulb $bulb)
    {
        $this->bulb = $bulb;
    }

    //// Only this trigger available when a bulb in OffState
    public function turnOn()
    {
        echo 'light on' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getOnState());
    }
    public function turnBrightest(){
        echo 'please swirch the light on first' . PHP_EOL;
    }

    //// null implement
    public function turnOff(){}
    public function convertFlash(){}
}

class OnState implements IState
{
    private $bulb;
    public function __construct(Bulb $bulb)
    {
        $this->bulb = $bulb;
    }

    //// null implement
    public function turnOn(){}

    public function turnOff()
    {
        echo 'light off' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getOffState());
    }

    public function turnBrightest()
    {
        echo 'light to brightest' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getBrightestState());
    }

    public function convertFlash()
    {
        echo 'light flashing' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getFlashState());
    }
}

class BrightestState implements IState
{
    private $bulb;
    public function __construct(Bulb $bulb)
    {
        $this->bulb = $bulb;
    }

    public function turnOn(){}

    public function turnOff()
    {
        echo 'light from brightest to dark .... light off' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getOffState());
    }

    public function turnBrightest()
    {
        echo 'light allready brightest' . PHP_EOL;
    }

    public function convertFlash()
    {
        echo 'light flashing with brightest light' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getFlashState());
    }
}

class FlashState implements IState
{
    private $bulb;
    public function __construct(Bulb $bulb)
    {
        $this->bulb = $bulb;
    }

    public function turnOn(){}

    public function turnOff()
    {
        echo 'light from flashing to dark .... light off' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getOffState());
    }

    public function turnBrightest()
    {
        echo 'light seem brighter gradually .... brightest' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getBrightestState());
    }

    public function convertFlash()
    {
        echo 'no flashing' . PHP_EOL;
        $this->bulb->setCurrentState($this->bulb->getOnState());
    }
}

/**
 * Class Bulb
 * Bulb is able to switch itself to diff state
 */
class Bulb
{
    public $luminance;

    private $onState;
    private $offState;
    private $brightestState;
    private $flashState;

    private $currentState;

    public function __construct()
    {
        // init all state
        $this->onState = new OnState($this);
        $this->offState = new OffState($this);
        $this->brightestState = new BrightestState($this);
        $this->flashState = new FlashState($this);
        // default state is offState
        $this->currentState = $this->offState;
        $this->luminance = '0%';
        $this->getSateInfo();
    }

    // use trigger methods
    public function turnOn()
    {
        $this->currentState->turnOn();
        $this->luminance = '80%';
        $this->getSateInfo();
    }

    public function turnOff()
    {
        $this->currentState->turnOff();
        $this->luminance = '0%';
        $this->getSateInfo();
    }

    public function turnBrightest()
    {
        $this->currentState->turnBrightest();
        $this->luminance = '100%';
        $this->getSateInfo();
    }

    public function convertFlash()
    {
        $this->currentState->convertFlash();
        $this->getSateInfo();
    }

    public function getSateInfo()
    {
        echo "luminance:$this->luminance".PHP_EOL;
        echo 'currentState:'.get_class($this->currentState).PHP_EOL;
    }

    //// Getter Setter
    public function setCurrentState(IState $currentState)
    {
        $this->currentState = $currentState;
    }


    public function getOnState()
    {
        return $this->onState;
    }

    public function getOffState()
    {
        return $this->offState;
    }

    public function getBrightestState()
    {
        return $this->brightestState;
    }

    public function getFlashState()
    {
        return $this->flashState;
    }

}

class Client {
    private $bulb;
    private $funcList;

    public function __construct(Bulb $bulb, $rand_times = 0)
    {
        $this->bulb = $bulb;
        $this->funcList = ['turnOn','turnBrightest','convertFlash','turnOff'];
        if($rand_times === 0){
            $this->seqTurn();
        }else {
            while($rand_times--) {
                $this->randTurn();
            }
        }
    }

    private function randTurn(){
        $rand_op = intval($this->randInt(1,4));
        $func = $this->funcList[$rand_op-1];
        echo PHP_EOL."operation:$func".PHP_EOL;
        $this->bulb->$func();
    }

    private function seqTurn(){
        foreach ($this->funcList as $func){
            echo PHP_EOL."operation:$func".PHP_EOL;
            $this->bulb->$func();
        }
    }

    private function randInt($min, $max)
    {
        return round (rand() / getrandmax() * ($max - $min) + $min);
    }
}

$worker = new Client(new Bulb());
//luminance:0%
//currentState:OffState
//
//operation:turnOn
//light on
//luminance:80%
//currentState:OnState
//
//operation:turnBrightest
//light to brightest
//luminance:100%
//currentState:BrightestState
//
//operation:convertFlash
//light flashing with brightest light
//luminance:100%
//currentState:FlashState
//
//operation:turnOff
//light from flashing to dark .... light off
//luminance:0%
//currentState:OffState