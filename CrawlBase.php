<?php

namespace ElasticCrawler;

use Elasticsearch\ClientBuilder;

/**
 * Base class, for crawler classes that have anything to do with the database
 *
 * @author Ooypunk
 */
class CrawlBase {

	protected $client;

	public function getClient() {
		if ($this->client === null) {
			// @todo Move to some configuration file
			$hosts = [
				'192.168.2.14',
			];
			$this->client = ClientBuilder::create()->setHosts($hosts)->build();
		}
		return $this->client;
	}

	public function update(array $body) {
		return $this->getClient()->update($body);
	}

}
