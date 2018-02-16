<?php

namespace UnderStated\Transitions;

use Closure;
use Fhaculty\Graph\Vertex;
use UnderStated\Contracts\MachineDriven;
use UnderStated\Machine;
use UnderStated\Exceptions;

/**
 * Class Transition
 * @package UnderStated\Transitions
 */
class Transition implements MachineDriven
{

    /**
     * @var string
     */
    public $slug;

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
    public $valid = true;

    /**
     * @var bool
     */
    protected $undirected = false;

    /**
     * @var Machine
     */
    protected $machine;

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
    public function getSlug()
    {
        if (isset($this->slug)) {
            return $this->slug;
        }
    }

    /**
     * Set transition ID.
     *
     * @param string $state
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * @throws Exception on error during transit.
     */
    public function onTransit() {
        if (!$this->canTransit()) {
            throw new Exceptions\TransitionException("Can't transit");
        }
    }

    /**
     * Called during transition.
     * 
     * @return bool
     */
    public function canTransit() {
        $this->valid = true;
        return $this->valid;
    }

}
