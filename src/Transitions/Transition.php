<?php

namespace OverStated\Transitions;

use Closure;
use Fhaculty\Graph\Vertex;
use OverStated\Contracts\MachineDriven;
use OverStated\Machine;
use OverStated\Exceptions;
use Illuminate\Support\Collection;

/**
 * Class Transition
 */
class Transition implements MachineDriven {

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
	 * @var Collection
	 */
	private $errors;

	/**
	 * @var array
	 */
	private $arguments = [];

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->errors = collect([]);
	}

	/**
	 * @param Machine $machine
	 *
	 * @return void
	 */
	public function setMachine(Machine $machine) {
		$this->machine = $machine;
	}

	/**
	 * @return Machine
	 */
	public function getMachine() {
		return $this->machine;
	}

	/**
	 * Get the transition ID
	 *
	 * @return string
	 */
	public function getSlug() {
		if (isset($this->slug)) {
			return $this->slug;
		}
	}

	/**
	 * Set transition ID.
	 *
	 * @param string $state
	 */
	public function setSlug($slug) {
		$this->slug = $slug;
	}

	/**
	 * Get transition nicename.
	 *
	 * @return string
	 */
	public function getNicename() {
		return $this->nicename;
	}

	/**
	 * Get transition description.
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Get transition origins.
	 *
	 * @return []string
	 */
	public function getFrom() {
		return (array) $this->from;
	}

	/**
	 * Get transition destination.
	 *
	 * @return string
	 */ public function getTo() {
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
	 * Adds error message.
	 *
	 * @param $message string
	 */
	public function addError(string $message) {
		$this->errors->push($message);
	}

	/**
	 * Get errors
	 */
	public function getErrors():Collection {
		return $this->errors;
	}

	/**
	 * Get errors and reset errors collection.
	 */
	public function getAndFlushErrors():Collection {
		$errors = $this->errors;
		$this->errors = collect([]);
		return $errors;
	}

	/**
	 * Called during transition.
	 *
	 * @throws Exception on error during transit.
	 */
	public function onTransit() {
		if (!$this->checkCanTransit()) {
			$errors = $this->getAndFlushErrors();
			$message = $errors->count() ? $errors->last() : "Can't transit";
			throw new Exceptions\TransitionException($message);
		}
	}

	/**
	 * Called during transition
	 */
	public final function checkCanTransit() : bool {

		$this->valid = true;

		// Call (possibly overloaded) "validatesTransition"
		$this->validatesTransition();

		try {
			$this->machine->validates($this->to);
		} catch (\Illuminate\Validation\ValidationException $exception) {
			foreach ($exception->validator->messages()->all() as $message) {
				$this->addError($message);
			}
		}

		if ($this->errors->isNotEmpty()) {
			$this->valid = false;
		}

		return $this->valid;
	}

	/**
	 * Called during transition.
	 * Meant to be overloaded.
	 *
	 * If you add some errors with $this->addError, the transition won't be
	 * possible.
	 */
	public function validatesTransition() : void {
	}

	/**
	 * Set Arguments
	 */
	public function setArguments(array $arguments) : void {
		$this->arguments = $arguments;
	}

	/**
	 * Get Arguments
	 */
	public function getArguments() : array {
		return $this->arguments;
	}

}
