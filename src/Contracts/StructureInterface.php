<?php

namespace OverStated\Contracts;

use OverStated\States\State;
use OverStated\Transitions\Transition;

/**
 * Interface StructureInterface
 * @package OverStated\Contracts
 */
interface StructureInterface {

	/**
	 * @param State $state
	 * @param int $location
	 * @return
	 */
	public function addState(State $state, $location = 0);

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

}
