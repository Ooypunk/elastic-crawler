<?php

namespace ElasticCrawler\Fetcher;

use ElasticCrawler\CrawlBase;
use ElasticCrawler\Fetcher\Page;

/**
 * Fetcher: get one page from database that is not processed yet (has no
 * http code field), then process it (get contents), and save it to DB.
 *
 * @author Ooypunk
 */
class Fetcher extends CrawlBase {

	public function run() {
		// Get page to do
		$body = $this->getFirstNextPage();
		$source = $body['_source'];
		if (!isset($source['url'])) {
			throw new \Exception("Fout! Geen URL gevonden\n");
		}

		// Use Page class to get contents of URL
		$page = new Page('https://dossier.dgict.nl/');
		$page->runCurl();

		// Compose update body
		$update_body = $this->getUpdateBody($body);

		// Add new data to updatebody
		$update_body['body']['doc']['http_code'] = $page->getHttpCode();
		$update_body['body']['doc']['content_type'] = $page->getContentType();
		$update_body['body']['doc']['body'] = $page->getBody();

		// Save new body to database
		$this->update($update_body);
	}

	private function getFirstNextPage() {
		$params = $this->getFirstNextPageParams();
		$response = $this->getClient()->search($params);
		$hits_nr = $response['hits']['total'];
		if ($hits_nr === 0) {
			throw new \Exception("Geen records gevonden\n");
		}

		return $response['hits']['hits'][0];
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

	private function getUpdateBody(array $body) {
		$update_body = [];
		$map = [
			'_index' => 'index',
			'_type' => 'type',
			'_id' => 'id',
		];
		foreach ($map as $from => $to) {
			$update_body[$to] = $body[$from];
		}
		$update_body['body']['doc'] = [];
		return $update_body;
	}

}
