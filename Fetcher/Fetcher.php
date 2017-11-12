<?php

namespace ElasticCrawler\Fetcher;

/**
 * Fetcher: get one page from database that is not processed yet (has no
 * http code field), then process it (get contents), and save it to DB.
 */
use Elasticsearch\ClientBuilder;

require 'vendor/autoload.php';
require dirname(__FILE__) . '/Page.php';

$client = ClientBuilder::create()->build();

$params = [
	"index" => "crawler",
	"type" => "crawler_1",
	"body" => [
		"from" => 0,
		"size" => 1,
		"query" => [
			"bool" => [
				"must_not" => [
					"exists" => [
						"field" => "http_code"
					]
				]
			]
		]
	]
];

$response = $client->search($params);
$hits_nr = $response['hits']['total'];
if ($hits_nr === 0) {
	die("Geen records gevonden\n");
}

$body = $response['hits']['hits'][0]['_source'];
if (!isset($body['url'])) {
	die("Fout! Geen URL gevonden\n");
}

$page = new Page($body['url']);
$page->runCurl();
var_dump($page->getBody());

