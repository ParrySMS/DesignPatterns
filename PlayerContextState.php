<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2019-8-24
 * Time: 10:16
 */
define('HEALTH_MAX', 100);
define('HEALTH_MIN', 0);
define('TIME_GAME_HOLDING', 15);
define('TIME_INIT', 1);
define('TIME_REVIVING', 1);
define('TIME_PROTECTED', 1);
define('TIME_PLAYING',1);
define('TIMES_RAND_DAMAGE',1); // P_dead = 5151/10201

abstract Class IGameState
{
    abstract public function turnPlaying();
    abstract public function turnReviving();
    abstract public function turnProtected();
}

class PlayerContext
{
    public $health;
    private $initial_state;
    private $playing_state;
    private $reviving_state;
    private $protected_state;
    private $current_state;

    public function __construct($health = HEALTH_MIN)
    {
        $this->initial_state = new InitialState($this);
        $this->playing_state = new PlayingState($this);
        $this->reviving_state = new RevivingState($this);
        $this->protected_state = new ProtectedState($this);

        $this->current_state = $this->initial_state;
        $this->health = $health;
    }

    //// some functional PlayerContext methods
    public function getTimeAndWait($sec)
    {
        echo date('H:i:s') . PHP_EOL . PHP_EOL;
        sleep($sec);
    }

    public function setRandDamage()
    {
        if($this->health>0) {
            $damage = $this->randInt(HEALTH_MIN, HEALTH_MAX);
            $this->health -= $damage;
            echo "Damage:$damage, HP:$this->health" . PHP_EOL;
        }
    }

    public function randInt($min, $max)
    {
        return round(rand() / getrandmax() * ($max - $min) + $min);
    }

    public function autoTurning($rand_damage_times = TIMES_RAND_DAMAGE)
    {
        $this->start();

        while ($rand_damage_times--){
            $this->setRandDamage();
        }

        if(0 > $this->health){
            $this->dead();
            $this->beenProtected();
        }
    }

    //// use state trigger methods
    public function start()
    {
        $this->current_state->turnPlaying();
    }

    public function dead()
    {
        $this->current_state->turnReviving();
    }

    public function beenProtected()
    {
        $this->current_state->turnProtected();
    }

    //// setter getter
    public function setCurrentState($current_state)
    {
        $this->current_state = $current_state;
    }

    public function getCurrentState()
    {
        return $this->current_state;
    }

    public function getInitialState()
    {
        return $this->initial_state;
    }

    public function getPlayingState()
    {
        return $this->playing_state;
    }

    public function getRevivingState()
    {
        return $this->reviving_state;
    }

    public function getProtectedState()
    {
        return $this->protected_state;
    }

}

class InitialState extends IGameState
{
    private $context;

    public function __construct(PlayerContext $context)
    {
        echo 'Game starting now' . PHP_EOL;
        $this->context = $context;
    }

    public function turnPlaying()
    {
        echo 'Storm the front. Ok, let\'s go! ' . PHP_EOL;
        $this->context->getTimeAndWait(TIME_INIT);
        $this->context->health = HEALTH_MAX;
        $this->context->setCurrentState($this->context->getPlayingState());
    }

    public function turnReviving(){}
    public function turnProtected(){}
}

class PlayingState extends IGameState
{
    private $context;

    private $msg = [
        'Stick together team. Hold this position.',
        'Cover me, Cover me, Cover me!',
        'You take the point. I cover you.',
        'Fall Back! Fall Back!',
        'Get in position and wait for my go!',
        'Storm the front! Go!',
        'Fire! Fire! Taking fire!'
        ];

    public function __construct(PlayerContext $context)
    {
        $this->context = $context;
    }

    public function turnPlaying(){
        $msg_index = intval($this->context->randInt(0,sizeof($this->msg)-1));
        echo 'Player :'.$this->msg[$msg_index].PHP_EOL;
        $this->context->getTimeAndWait(TIME_PLAYING);
    }

    public function turnReviving()
    {
        echo 'Taking fire, need assistance! I \'m down.' . PHP_EOL;
        $this->context->setCurrentState($this->context->getRevivingState());
    }

    public function turnProtected(){}
}

class RevivingState extends IGameState
{
    private $context;

    public function __construct(PlayerContext $context)
    {
        $this->context = $context;
    }

    public function turnPlaying(){}
    public function turnReviving(){}
    public function turnProtected(){
        echo 'Reviviing...' . PHP_EOL;
        $this->context->getTimeAndWait(TIME_REVIVING);
        $this->context->setCurrentState($this->context->getProtectedState());
    }
}

class ProtectedState extends IGameState
{
    private $context;

    public function __construct(PlayerContext $context)
    {
        $this->context = $context;
    }
    public function turnReviving(){}
    public function turnProtected(){}
    public function turnPlaying(){
        echo 'Player is invulnerable now.' . PHP_EOL;
        $this->context->getTimeAndWait(TIME_PROTECTED);
        echo 'Player is vulnerable now.' . PHP_EOL;
        $this->context->getTimeAndWait(0);
        $this->context->health = HEALTH_MAX;
        $this->context->setCurrentState($this->context->getPlayingState());
    }
}

class Client
{
    private $context;

    public function __construct(PlayerContext $context)
    {
        $this->context = $context;
        $this->startGame();
    }

    private function startGame($sec = TIME_GAME_HOLDING)
    {
        $this->context->start();
        $start = time();
        while (1) {
            $this->context->autoTurning();
            sleep(TIME_PLAYING);
            if(time()-$start > $sec){
                break;
            }
        }
        echo 'Game End'.PHP_EOL;
    }
}

$worker = new Client(new PlayerContext());
//Game starting now
//Storm the front. Ok, let's go!
//15:26:21
//
//Player :You take the point. I cover you.
//15:26:22
//
//Damage:81, HP:19
//Player :Get in position and wait for my go!
//15:26:24
//
//Damage:47, HP:-28
//Taking fire, need assistance! I 'm down.
//Reviviing...
//15:26:25
//
//Player is invulnerable now.
//15:26:27
//
//Player is vulnerable now.
//15:26:28
//
//Damage:70, HP:30
//Player :Stick together team. Hold this position.
//15:26:29
//
//Damage:91, HP:-61
//Taking fire, need assistance! I 'm down.
//Reviviing...
//15:26:30
//
//Player is invulnerable now.
//15:26:32
//
//Player is vulnerable now.
//15:26:33
//
//Damage:36, HP:64
//Player :Get in position and wait for my go!
//15:26:34
//
//Damage:85, HP:-21
//Taking fire, need assistance! I 'm down.
//Reviviing...
//15:26:35
//
//Player is invulnerable now.
//15:26:37
//
//Player is vulnerable now.
//15:26:38
//
//Damage:42, HP:58
//Game End