<?php

return [
	'api_token' => env('WS_API_TOKEN'),
	'access_token' => env('WS_ACCESS_TOKEN'),
	'delete_scrapingjobs' => env('WS_DELETE_SCRAPING_JOBS', 1),
	'import_sitemap_prefix' => env('WS_IMPORT_SITEMAP_PREFIX', ""),
	'data_import_job_classname' => env('WS_IMPORT_JOB_CLASSNAME', '\App\Jobs\ImportData'),
];