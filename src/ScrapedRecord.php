<?php

namespace WebScraper\LaravelDataImporter;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ScrapedRecord
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $scrapingjob_id
 * @property int $sitemap_id
 * @property string $sitemap_name
 * @property string $web_scraper_order
 * @property string $data
 * @property int $time_created
 * @method static \Illuminate\Database\Eloquent\Builder|\WebScraper\LaravelDataImporter\ScrapedRecord whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\WebScraper\LaravelDataImporter\ScrapedRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\WebScraper\LaravelDataImporter\ScrapedRecord whereScrapingjobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\WebScraper\LaravelDataImporter\ScrapedRecord whereSitemapId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\WebScraper\LaravelDataImporter\ScrapedRecord whereSitemapName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\WebScraper\LaravelDataImporter\ScrapedRecord whereTimeCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\WebScraper\LaravelDataImporter\ScrapedRecord whereWebScraperOrder($value)
 */
class ScrapedRecord extends Model {

	protected $table = "scraped_records";

	protected $casts = [
		'data' => 'array',
	];
}
