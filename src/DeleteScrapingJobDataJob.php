<?php

namespace WebScraper\LaravelDataImporter;

use \DB;
use \Log;
use WebScraper\ApiClient\Client;
use League\Csv\Reader;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Downloads and stores data in scraped_records table
 * @package App\Jobs
 */
class DeleteScrapingJobDataJob implements ShouldQueue {

	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $scrapingJobId;

	protected $sitemapName;

	protected $sitemapId;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($scrapingJobId, $sitemapName, $sitemapId) {

		$this->scrapingJobId = (int)$scrapingJobId;
		$this->sitemapName = $sitemapName;
		$this->sitemapId = (int)$sitemapId;
	}

	/**
	 * Execute the job
	 */
	public function handle() {

		$scrapingJobId = $this->scrapingJobId;
		$client = $this->getApiClient();
		Log::debug("Deleting scraping job", ['scrapingJobId' => $scrapingJobId]);
		$client->deleteScrapingJob($scrapingJobId);
	}

	/**
	 * @return Client
	 */
	public function getApiClient() {

		$apiClient = new Client([
			'token' => config('webscraperio-data-importer.api_token'),
		]);

		return $apiClient;
	}
}
