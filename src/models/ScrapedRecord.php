<?php

namespace App;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScrapedRecord whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScrapedRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScrapedRecord whereScrapingjobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScrapedRecord whereSitemapId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScrapedRecord whereSitemapName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScrapedRecord whereTimeCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ScrapedRecord whereWebScraperOrder($value)
 */
class ScrapedRecord extends Model {

	protected $table = "scraped_records";

	protected $casts = [
		'data' => 'array',
	];
}
