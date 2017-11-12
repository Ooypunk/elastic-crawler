<?php

namespace ElasticCrawler;

/**
 * For now: fire one other class (Fetcher, Parser).
 * To do: automate firing off these classes, depending on database contents
 */
class Crawler {

	public function __construct() {
		if (!isset($_SERVER['argv']) || !is_array($_SERVER['argv'])) {
			throw new Exception('Arguments not found (not set)');
		}
		print "<pre>";
		print_r($_SERVER['argv']);
		die('@debug in ' . __FILE__ . ' @' . __LINE__ . "\n");
	}

}

new Crawler();
