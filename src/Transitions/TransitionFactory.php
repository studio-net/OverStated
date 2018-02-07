<?php namespace UnderStated\Transitions;

use Closure;
use UnderStated\Exceptions\TransitionException;

/**
 * Class TransitionFactory
 * @package FSM\Transitions
 */
class TransitionFactory
{
    /**
     * @param $id
     * @param $resolvable
     *
     * @return Transition
     *
     * @throws StateException
     */
    public function create($resolvable)
    {
        $transition = $this->buildTransition($resolvable);

        return $transition;
    }

    /**
     * @param $resolvable
     * @return State
     */
    private function buildTransition($resolvable)
    {
        $class = '\\' . ltrim($resolvable, '\\');
        return new $class();
    }

}
