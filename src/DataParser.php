<?php


namespace WebScraperIO\LaravelDataImporter;


class DataParser {

	/**
	 * @var integer
	 */
	protected $scrapingJobId;

	/**
	 * @var string
	 */
	protected $sitemapName;

	/**
	 * @var integer
	 */
	protected $sitemapId;

	public function __construct($scrapingJobId, $sitemapName, $sitemapId) {

		$this->scrapingJobId = (int) $scrapingJobId;
		$this->sitemapName = $sitemapName;
		$this->sitemapId = (int) $sitemapId;
	}

	/**
	 * Parse record before inserting it into db.
	 * Return false to skip insert
	 * @param $recordData
	 * @return array|boolean
	 */
	public function parseRecord(array $recordData) {

		return $recordData;
	}
}