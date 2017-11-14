<?php

namespace WebScraperIO\LaravelDataImporter;

use Illuminate\Support\ServiceProvider;

class LaravelDataImporterServiceProvider extends ServiceProvider {

	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot() {

		$this->loadRoutesFrom(__DIR__ . '/routes.php');
//		$this->loadMigrationsFrom(__DIR__ . '/migrations');
		$this->publishes([
			__DIR__ . '/migrations' => database_path('migrations'),
		], 'migrations');
	}

	/**
	 * Register any package services.
	 *
	 * @return void
	 */
	public function register() {
		//
	}
}