<?php

namespace UnderStated\Transitions;

use Closure;
use Fhaculty\Graph\Vertex;
use UnderStated\Contracts\MachineDriven;
use UnderStated\Machine;

/**
 * Class Transition
 * @package UnderStated\Transitions
 */
class Transition implements MachineDriven
{

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $nicename;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $from;

    /**
     * @var string
     */
    public $to;

    /**
     * @var bool
     */
    protected $undirected = false;

    /**
     * @var Machine
     */
    protected $machine;

    /**
     * @var array
     */
    protected $closures = [];

    /**
     * @var array
     */
    protected $boundEvents = [];

    /**
     * @param Machine $machine
     *
     * @return void
     */
    public function setMachine(Machine $machine)
    {
        $this->machine = $machine;
    }

    /**
     * @return Machine
     */
    public function getMachine()
    {
        return $this->machine;
    }

    /**
     * Get the state ID
     *
     * @return string
     */
    public function getId()
    {
        if (isset($this->state)) {
            return $this->state;
        }

        $className = str_replace('\\', '', snake_case(class_basename($this)));

        return $this->state = str_replace('_state', '', $className);
    }

    /**
     * Set transition ID.
     *
     * @param string $state
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get transition nicename.
     * 
     * @return string
     */
    public function getNicename()
    {
        return $this->nicename;
    }

    /**
     * Get transition description.
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get transition origin.
     * 
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Get transition destination.
     * 
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Get the bound events on the transition.
     *
     * @return array
     */
    public function getBoundEvents()
    {
        return $this->boundEvents;
    }

    /**
     * Helper function to handle a state method.
     *
     * @param string $handle
     * @param array $args
     *
     * @return mixed
     */
    public function handle($handle, $args = [])
    {
        return $this->machine->handle($handle, $args);
    }

    /**
     * @param $event
     * @param array $args
     */
    public function fire($event, $args = [])
    {
        $this->machine->fire($event, $args);
    }

    /**
     * @param $event
     * @param $callback
     */
    public function listen($event, $callback)
    {
        $this->boundEvents[] = $event;

        $this->machine->listen($event, $callback);
    }

    /**
     * Forget bound events
     *
     * @param $events
     */
    public function forget($events)
    {
        $this->machine->forget($events);
    }

    /**
     * @param string $method
     * @param Closure $closure
     */
    public function addClosure($method, Closure $closure)
    {
        $this->closures[$method] = $closure;
    }

    /**
     * onEnter handler for state.
     *
     * @param State $state
     * @return bool
     */
    public function onEnter(State $state)
    {
        return true;
    }

    /**
     * onExit handler for state
     *
     * @param State $state
     * @return bool
     */
    public function onExit(State $state)
    {
        return true;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (array_key_exists($method, $this->closures)) {
            array_unshift($args, $this);
            return call_user_func_array($this->closures[$method], $args);
        }

        return null;
    }
}
