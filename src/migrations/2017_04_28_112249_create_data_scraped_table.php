<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DataTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		Schema::create('data_scraped', function (Blueprint $table) {

			$table->bigIncrements('id');
			$table->integer('scrapingjob_id');
			$table->integer('sitemap_id');
			$table->string('sitemap_name');
			$table->string('web_scraper_order');
			$table->json('data');
			$table->unsignedInteger('time_created');

			$table->index(['scrapingjob_id']);
			$table->index(['sitemap_id']);
			$table->index(['sitemap_name']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::drop("data_scraped");
	}
}
