<?php

namespace WebScraper\LaravelDataImporter;

use Illuminate\Database\Eloquent\Model;

class ScrapedRecord extends Model {

	protected $table = "scraped_records";

	public $timestamps = false;

	protected $casts = [
		'data' => 'object',
	];
}
