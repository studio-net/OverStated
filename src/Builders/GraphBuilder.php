<?php

namespace OverStated\Builders;

use Fhaculty\Graph\Graph;
use OverStated\Adapters\GraphStructure;
use OverStated\Contracts\MachineBuilder;
use OverStated\Machine;
use OverStated\States\StateFactory;
use OverStated\Transitions\TransitionFactory;
use OverStated\Transitions\Transition;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GraphBuilder
 * @package OverStated\Builders
 */
class GraphBuilder implements MachineBuilder {
	/**
	 * @var GraphStructure
	 */
	protected $graph;

	/**
	 * @var Machine
	 */
	protected $machine;

	/**
	 * @var StateFactory
	 */
	private $stateFactory;

	/**
	 * Construct the builder
	 */
	public function __construct() {
		$this->graph = new GraphStructure(new Graph);
		$this->stateFactory = new StateFactory();
		$this->transitionFactory = new TransitionFactory();
	}

	/**
	 * Create a new graph instance
	 *
	 * @param Machine $machine
	 *
	 * @return $this
	 */
	public function create(Machine $machine = null) {
		$machine = $machine ? : app()->make(Machine::class);

		$this->machine = $machine;

		$this->graph->setMachine($machine);

		return $this;
	}

	/**
	 * Add a new state
	 *
	 * @param $id
	 * @param $resolvable
	 * @param int $location
	 * @return $this
	 */
	public function state($resolvable = null, $location = 0) {
		$state = $this->stateFactory->create($resolvable);

		$this->graph->addState($state, $location);

		return $this;
	}

	/**
	 * Add a list of states
	 *
	 * @param array $states
	 * @return mixed
	 */
	public function states(array $states) {
		foreach ($states as $state) {
			call_user_func_array([$this, 'state'], $state);
		}

		return $this;
	}


	/**
	 * Add a new transition
	 *
	 * @param Transition $transition
	 * @return $this
	 */
	public function transition($transition) {
		$transition = $this->transitionFactory->create($transition);
		$transition->setMachine($this->machine);
		$this->graph->addTransition($transition);
		return $this;
	}

	/**
	 * Add associated model
	 *
	 * @param Model $model
	 * @return $this
	 */
	public function model($model) {
		$this->machine->setModel($model);
		return $this;
	}

	/**
	 * Add a list of transitions
	 *
	 * @param array $transitions
	 * @return mixed
	 */
	public function transitions(array $transitions) {
		foreach ($transitions as $transition) {
			call_user_func_array([$this, 'transition'], $transition);
		}

		return $this;
	}

	/**
	 * Get the built machine
	 * @return Machine
	 */
	public function getMachine() {
		$machine = $this->machine ? : $this->create()->machine;

		$machine->setStructure($this->graph);

		return $machine;
	}
}
