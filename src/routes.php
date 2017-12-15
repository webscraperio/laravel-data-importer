<?php

Route::post('/handle-web-scraper-io-notification/{accessToken}', function (Illuminate\Http\Request $request, $accessToken) {

	$allowedAccessToken = config('webscraperio-data-importer.access_token');
	if($allowedAccessToken !== $accessToken) {
		return response("Unauthorized", 401);
	}

	$scrapingJobId = (int) $request->input('scrapingjob_id');
	$sitemapName = $request->input('sitemap_name');
	$sitemapId = $request->input('sitemap_id');
	$status = $request->input('status');

	// only import sitemaps that match predefined prefix
	$prefix = config("webscraperio-data-importer.import_sitemap_prefix");
	if(strlen($prefix) > 0 && strpos($sitemapName, $prefix) !== 0) {
		return "skipped";
	}

	// dispatch a sitemap import job
	$downloadClassname = config('webscraperio-data-importer.data_download_job_classname');
	$job = new $downloadClassname($scrapingJobId, $sitemapName, $sitemapId);
	dispatch($job);

	return "ok";
});