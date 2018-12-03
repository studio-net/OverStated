<?php

namespace OverStated\Contracts;

use OverStated\Machine;

/**
 * Interface MachineDriven
 * @package FSM\Contracts
 */
interface MachineDriven {

	/**
	 * Set the machine instance to the state
	 *
	 * @param Machine $machine
	 */
	public function setMachine(Machine $machine);

}
