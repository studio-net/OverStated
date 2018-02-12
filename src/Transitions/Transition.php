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
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var []string
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
     * Get the transition ID
     *
     * @return string
     */
    public function getId()
    {
        if (isset($this->id)) {
            return $this->id;
        }
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
     * Get transition origins.
     * 
     * @return []string
     */
    public function getFrom()
    {
        return (array) $this->from;
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
     * Is transition undirected ?
     * 
     * @return bool
     */
    public function isUndirected() {
        return $this->undirected;
    }

    /**
     * Called during transition.
     * 
     * @throws Exception if transition is not possible.
     */
    public function onTransit() {}

}
