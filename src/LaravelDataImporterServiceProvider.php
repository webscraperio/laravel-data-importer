<?php

namespace WebScraper\LaravelDataImporter;

use Illuminate\Support\ServiceProvider;

class LaravelDataImporterServiceProvider extends ServiceProvider {

	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot() {

		$this->loadRoutesFrom(__DIR__ . '/routes.php');

		$this->publishes([
			__DIR__ . '/migrations' => database_path('migrations'),
		], 'migrations');

		$this->publishes([
			__DIR__.'/config/webscraperio-data-importer.php' => config_path('webscraperio-data-importer.php')
		], 'config');

		$this->publishes([
			__DIR__.'/models/ScrapedRecord.php' => app_path('ScrapedRecord.php')
		], 'models');
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