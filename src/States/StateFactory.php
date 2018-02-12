<?php namespace UnderStated\States;

use Closure;
use UnderStated\Exceptions\StateException;

/**
 * Class StateFactory
 * @package FSM\States
 */
class StateFactory
{
    /**
     * @param $id
     * @param $resolvable
     *
     * @return State
     *
     * @throws StateException
     */
    public function create($resolvable = null)
    {
        $state = $this->buildState($resolvable);
        return $state;
    }

    /**
     * @param $resolvable
     * @return State
     */
    private function buildState($resolvable)
    {
        $state = $this->newStateFromClass($resolvable);
        return $state;
    }

    /**
     * @param $resolvable
     * @return State
     */
    private function newStateFromClass($resolvable) : State
    {
        $class = '\\' . ltrim($resolvable, '\\');
        return new $class();
    }

}
