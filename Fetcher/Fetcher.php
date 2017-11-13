<?php

namespace ElasticCrawler\Fetcher;

use ElasticCrawler\CrawlBase;
use Elasticsearch\ClientBuilder;
use ElasticCrawler\Fetcher\Page;

/**
 * Fetcher: get one page from database that is not processed yet (has no
 * http code field), then process it (get contents), and save it to DB.
 *
 * @author Ooypunk
 */
class Fetcher extends CrawlBase {

	public function run() {
		$body = $this->getFirstNextPage();
		if (!isset($body['url'])) {
			throw new \Exception("Fout! Geen URL gevonden\n");
		}

		$page = new Page($body['url']);
		$page->runCurl();

		var_dump($page->getBody());
		die('@debug in ' . __FILE__ . ' @' . __LINE__ . "\n");
	}

	private function getFirstNextPage() {
		// @todo Move to some configuration file
		$hosts = [
			'192.168.56.101',
		];
		$client = ClientBuilder::create()->setHosts($hosts)->build();

		$params = $this->getFirstNextPageParams();
		$response = $client->search($params);
		$hits_nr = $response['hits']['total'];
		if ($hits_nr === 0) {
			throw new \Exception("Geen records gevonden\n");
		}

		return $response['hits']['hits'][0]['_source'];
	}

	private function getFirstNextPageParams() {
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
		return $params;
	}

}
