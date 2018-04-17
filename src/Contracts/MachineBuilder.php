<?php namespace OverStated\Contracts;

use OverStated\Machine;

/**
 * Interface MachineBuilder
 * @package FSM\Contracts
 */
interface MachineBuilder
{
    /**
     * Create a new FSM instance
     *
     * @param Machine $machine
     *
     * @return $this
     */
    public function create(Machine $machine = null);

    /**
     * Add a new state
     *
     * @param $resolvable
     * @param int $location
     * @return $this
     */
    public function state($resolvable = null, $location = 0);

    /**
     * Add a list of states
     *
     * @param array $states
     * @return mixed
     */
    public function states(array $states);

    /**
     * Add a new transition
     *
     * @param Transition $transition
     * @return $this
     */
    public function transition($transition);


    /**
     * Add a list of transitions
     *
     * @param array $transitions
     * @return mixed
     */
    public function transitions(array $transitions);

    /**
     * Get the built machine
     *
     * @return Machine
     */
    public function getMachine();
}
