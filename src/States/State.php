<?php

namespace OverStated\States;

use Closure;
use Fhaculty\Graph\Vertex;
use OverStated\Contracts\MachineDriven;
use OverStated\Machine;

/**
 * Class State
 * @package OverStated\States
 */
abstract class State implements MachineDriven {

	/**
	 * Initial state constant
	 */
	const INITIAL = 1;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var Vertex
	 */
	protected $vertex;

	/**
	 * @var Machine
	 */
	protected $machine;

	/**
	 * @param Machine $machine
	 *
	 * @return void
	 */
	final public function setMachine(Machine $machine) {
		$this->machine = $machine;
	}

	/**
	 * @return Machine
	 */
	final public function getMachine() {
		return $this->machine;
	}

	/**
	 * Set the graph vertex for this state.
	 *
	 * @param Vertex $vertex
	 *
	 * @return void
	 */
	final public function setVertex(Vertex $vertex) {
		$this->vertex = $vertex;
	}

	/**
	 * Get the graph vertex associated with this state
	 *
	 * @return Vertex
	 */
	final public function getVertex() {
		return $this->vertex;
	}

	/**
	 * Get the state ID
	 *
	 * @return string
	 */
	final public function getId() {
		if (isset($this->id)) {
			return $this->id;
		}

		$className = str_replace('\\', '', snake_case(class_basename($this)));

		return $this->id = str_replace('_state', '', $className);
	}

	/**
	 * Set the state ID
	 *
	 * @param string $id
	 */
	final public function setId($id) {
		$this->id = $id;
	}

	/**
	 * Validate the State.
	 *
	 * Meant to be overriden.
	 *
	 * @throws \Exception
	 */
	public function validateState() {
	}

}
