<?php
require "Factory.php"
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
	public function templateMethod(){
		$this->addMsgForGraph();
		$this->addMsgForTM();
	}
	//implemented by other class
	protected abstract function addMsgForGraph();
	protected abstract function addMsgForTM();
}

class TMFactory extends TMBase
{
	//todo: TMFactory
}



class TMClient
{
    public function __construct(){
        $TMFactory = new TMFactory();
		//the order of execution is defined in templateMethod()
        TMFactory->templateMethod();
    }
}
