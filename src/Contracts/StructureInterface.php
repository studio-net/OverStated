<?php namespace UnderStated\Contracts;

use UnderStated\States\State;
use UnderStated\Transitions\Transition;

/**
 * Interface StructureInterface
 * @package UnderStated\Contracts
 */
interface StructureInterface
{

    /**
     * @param $id
     * @param State $state
     * @param int $location
     * @return
     */
    public function addState($id, State $state, $location = 0);

    /**
     * @param string $state
     * @return State
     */
    public function getState($state);

    /**
     * Get the initial state
     *
     * @param null $override
     * @return mixed
     */
    public function getInitialState($override = null);

    /**
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function canTransitionFrom($from, $to);

    /**
     * @param string $state
     * @return mixed
     */
    public function getTransitionsFrom($state);

    /**
     * @param Transition $transition
     * @return mixed
     */
    public function transition($transition);

}
