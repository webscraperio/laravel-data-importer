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
 * Downloads and stores data in data_scraped table
 * @package App\Jobs
 */
class DownloadDataJob implements ShouldQueue {

	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $scrapingJobId;

	protected $sitemapName;

	protected $sitemapId;

	protected $insertBatchLimit = 100;

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

		DB::transaction(function () {

			try {
				$scrapingJobId = $this->scrapingJobId;

				// temporary file where csv data will be stored
				$tmpDownloadFile = tempnam("/tmp", "scrapingjob-data-$scrapingJobId-csv");

//				$this->deleteOldData();
				$this->downloadCsv($tmpDownloadFile);
				$this->storeDataInDB($tmpDownloadFile);
				$this->deleteScrapingJob();
				$this->scheduleDataImport();

			} finally {
				// remove temporary file
				unlink($tmpDownloadFile);
			}
		});
	}

	/**
	 * Delete old records from db in case we are retrying the data import.
	 * Technically this cannot happen because previous job was running in a transaction.
	 */
	public function deleteOldData() {

		$scrapingJobId = $this->scrapingJobId;
		$oldDataColumntCount = DB::table('data_scraped')->where('scrapingjob_id', $scrapingJobId)->count();

		if ($oldDataColumntCount > 0) {
			Log::notice("Overwriting old data", ['scrapingJobId' => $scrapingJobId]);
			DB::table('data_scraped')->where('scrapingjob_id', $scrapingJobId)->delete();
		}
	}

	public function downloadCsv($tmpDownloadFile) {

		$scrapingJobId = $this->scrapingJobId;
		Log::debug("Downloading CSV", ['scrapingJobId' => $scrapingJobId]);
		$client = $this->getApiClient();
		$client->downloadScrapingJobCSV($this->scrapingJobId, $tmpDownloadFile);
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

	public function storeDataInDB($tmpDownloadFile) {

		$scrapingJobId = $this->scrapingJobId;
		$sitemapId = $this->sitemapId;
		$sitemapName = $this->sitemapName;

		// read CSV file
		$records = Reader::createFromPath($tmpDownloadFile)->fetchAssoc();

		Log::debug("Inserting data", ['scrapingJobId' => $scrapingJobId]);
		$webScraperOrderKey = "\xef\xbb\xbfweb-scraper-order";
		$insertBatchLimit = $this->insertBatchLimit;
		$insertData = [];
		$now = time();

		// import records in batches
		foreach ($records as $record) {

			$webScraperOrder = $record[$webScraperOrderKey];
			unset($record[$webScraperOrderKey]);

			// replace "null" values with null
			foreach ($record as $key => $value) {
				if ($value === "null") {
					$record[$key] = null;
				}
			}

			$insertData[] = [
				'scrapingjob_id' => $scrapingJobId,
				'sitemap_id' => $sitemapId,
				'sitemap_name' => $sitemapName,
				'web_scraper_order' => $webScraperOrder,
				'data' => json_encode($record, JSON_UNESCAPED_SLASHES),
				'time_created' => $now,
			];

			if (count($insertData) >= $insertBatchLimit) {
				DB::table('data_scraped')->insert($insertData);
				$insertData = [];
			}
		}
		DB::table('data_scraped')->insert($insertData);
		Log::debug("Data insert completed", ['scrapingJobId' => $scrapingJobId]);
	}

	/**
	 * delete scraping job because you probably don't need it
	 */
	public function deleteScrapingJob() {

		$scrapingJobId = $this->scrapingJobId;
		$client = $this->getApiClient();
		$deleteScrapingJob = config("webscraperio-data-importer.delete_scrapingjobs");
		if ($deleteScrapingJob) {
			Log::debug("Deleting scraping job", ['scrapingJobId' => $scrapingJobId]);
			$client->deleteScrapingJob($scrapingJobId);
		}
	}

	/**
	 * Continue data import by scheduling a data import job
	 */
	public function scheduleDataImport() {

		$scrapingJobId = $this->scrapingJobId;
		$sitemapId = $this->sitemapId;
		$sitemapName = $this->sitemapName;

		Log::debug("Scheduling data import", ['scrapingJobId' => $scrapingJobId]);
		$importJobClassname = config('webscraperio-data-importer.data_import_job_classname');
		$job = new $importJobClassname($scrapingJobId, $sitemapName, $sitemapId);
		dispatch($job);
	}
}
