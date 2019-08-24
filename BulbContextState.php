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
    public function __construct(BulbContext $bulb)
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
    public function __construct(BulbContext $bulb)
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
    public function __construct(BulbContext $bulb)
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
    public function __construct(BulbContext $bulb)
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
class BulbContext
{
    public $luminance;

    private $on_state;
    private $off_state;
    private $brightest_state;
    private $flash_state;

    private $current_state;

    public function __construct()
    {
        // init all state
        $this->on_state = new OnState($this);
        $this->off_state = new OffState($this);
        $this->brightest_state = new BrightestState($this);
        $this->flash_state = new FlashState($this);
        // default state is offState
        $this->current_state = $this->off_state;
        $this->luminance = '0%';
        $this->getSateInfo();
    }

    // use trigger methods
    public function turnOn()
    {
        $this->current_state->turnOn();
        $this->luminance = '80%';
        $this->getSateInfo();
    }

    public function turnOff()
    {
        $this->current_state->turnOff();
        $this->luminance = '0%';
        $this->getSateInfo();
    }

    public function turnBrightest()
    {
        $this->current_state->turnBrightest();
        $this->luminance = '100%';
        $this->getSateInfo();
    }

    public function convertFlash()
    {
        $this->current_state->convertFlash();
        $this->getSateInfo();
    }

    public function getSateInfo()
    {
        echo "luminance:$this->luminance".PHP_EOL;
        echo 'currentState:'.get_class($this->current_state).PHP_EOL;
    }

    //// Getter Setter
    public function setCurrentState(IState $current_state)
    {
        $this->current_state = $current_state;
    }


    public function getOnState()
    {
        return $this->on_state;
    }

    public function getOffState()
    {
        return $this->off_state;
    }

    public function getBrightestState()
    {
        return $this->brightest_state;
    }

    public function getFlashState()
    {
        return $this->flash_state;
    }

}

class Client {
    private $bulb;
    private $func_list;

    public function __construct(BulbContext $bulb, $rand_times = 0)
    {
        $this->bulb = $bulb;
        $this->func_list = ['turnOn','turnBrightest','convertFlash','turnOff'];
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
        $func = $this->func_list[$rand_op-1];
        echo PHP_EOL."operation:$func".PHP_EOL;
        $this->bulb->$func();
    }

    private function seqTurn(){
        foreach ($this->func_list as $func){
            echo PHP_EOL."operation:$func".PHP_EOL;
            $this->bulb->$func();
        }
    }

    private function randInt($min, $max)
    {
        return round (rand() / getrandmax() * ($max - $min) + $min);
    }
}

$worker = new Client(new BulbContext());
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