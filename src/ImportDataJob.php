<?php

namespace WebScraperIO\LaravelDataImporter;

use DB;
use Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class ImportDataJob implements ShouldQueue {

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
		$this->sitemapId = (int) $sitemapId;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {

		DB::transaction(function () {

			$scrapingJobId = $this->scrapingJobId;
			$sitemapName = $this->sitemapName;
			$sitemapId = $this->sitemapId;
			$insertBatchLimit = $this->insertBatchLimit;

			$records = DB::table('data_scraped')
				->where('scrapingjob_id', $scrapingJobId)
				->get();

			$dataParser = new DataParser($scrapingJobId, $sitemapName, $sitemapId);

			$insertData = [];
			// import records in batches
			foreach ($records as $record) {

				$recordOriginalData = json_decode($record->data, true);
				$recordData = $dataParser->parseRecord($recordOriginalData);

				// some records need to be skipped
				if ($recordData === false) {
					Log::notice("Skipping data import", [
						'scrapingJobId' => $this->scrapingJobId,
						'sitemapName' => $this->sitemapName,
						'data' => $recordOriginalData,
					]);
					continue;
				}

				// add sitemap information
				$recordData['scrapingjob_id'] = $record->scrapingjob_id;
				$recordData['sitemap_id'] = $record->sitemap_id;
				$recordData['sitemap_name'] = $record->sitemap_name;
				$recordData['web_scraper_order'] = $record->web_scraper_order;
				$recordData['time_created'] = $record->time_created;

				$insertData[] = $recordData;

				if (count($insertData) >= $insertBatchLimit) {

					// insert records
					DB::table('data_imported')->insert($insertData);
					$insertData = [];
				}
			}

			// finish inserting data
			DB::table('data_imported')->insert($insertData);
		});
	}
}
