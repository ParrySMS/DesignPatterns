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
    public function turnColor();
}

//todo: 2 color light