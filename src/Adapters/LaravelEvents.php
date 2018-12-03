<?php namespace OverStated\Adapters;

use OverStated\Contracts\EventInterface;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class LaravelEvents
 * @package OverStated\Adapters
 */
class LaravelEvents implements EventInterface {

	/**
	 * @var Dispatcher
	 */
	protected $dispatcher;

	/**
	 * @param Dispatcher $dispatcher
	 */
	public function __construct(Dispatcher $dispatcher) {
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @param $name
	 * @param callable $callback
	 * @return void
	 */
	public function listen($name, callable $callback) {
		$this->dispatcher->listen($name, $callback);
	}

	/**
	 * @param $name
	 * @param array $args
	 * @return void
	 */
	public function fire($name, $args = []) {
		$this->dispatcher->dispatch($name, $args);
	}

	/**
	 * @param $names
	 * @return void
	 */
	public function forget($names) {
		$this->dispatcher->forget($names);
	}

}
