<?php
namespace UnderStated\Support;

use UnderStated\Builders\GraphBuilder;

/**
 * Stateful
 *
 * @see BaseModel
 */
trait Stateful {

	/** @var Machine[] $fsms */
	protected $fsms = [];

	/**
	 * Create FSMs to manage FSM-based-fields.
	 */
	private function registerFsms() {

		if (empty($this->fsmDefinitions)) {
			throw new \Exception("FSM not defined.");
		}

		foreach ($this->fsmDefinitions as $field => $fsmInfos) {
			$builder = new GraphBuilder();
			$builder = $builder->create();

			foreach ($fsmInfos['states'] as $state) {
				$builder->state($state);
			}
			foreach ($fsmInfos['transitions'] as $transition) {
				$builder->transition($transition);
			}
			$this->fsms[$field] = $builder->getMachine();

			if (!empty($this->$field)) {
				$this->fsms[$field]->initialise($this->status);
			}
		}

	}

	/**
	 * Get requested FSM.
	 * 
	 * @param string $field
	 * @return UnderStated\Machine
	 */
	public function getFsm($field) : \UnderStated\Machine {

		if (empty($this->fsms)) {
			$this->registerFsms();
		}
		return $this->fsms[$field];
	}

}