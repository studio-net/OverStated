<?php

namespace OverStated\Providers;

use Illuminate\Support\ServiceProvider;
use OverStated\Adapters\LaravelEvents;
use OverStated\Contracts\EventInterface;

/**
 * Class OverStatedServiceProvider
 * @package OverStated\Providers
 */
class OverStatedServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->bind(
			EventInterface::class,
			LaravelEvents::class
		);
	}

}
