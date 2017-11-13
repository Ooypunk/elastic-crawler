<?php

namespace ElasticCrawler;

if (file_exists('vendor/autoload.php')) {
	require 'vendor/autoload.php';
} else {
	die("Run Composer first (composer install)\n");
}

/**
 * For now: fire one other class (Fetcher, Parser).
 * To do: automate firing off these classes, depending on database contents
 *
 * @author Ooypunk
 */
class Crawler {

	private $args;

	public function __construct() {
		$this->parseArguments();
		switch ($this->args[0]) {
			case 'fetcher':
				$instance = new Fetcher\Fetcher();
				break;
		}
		$instance->run();
		die("Done\n");
	}

	private function parseArguments() {
		if (!isset($_SERVER['argv']) || !is_array($_SERVER['argv'])) {
			throw new \Exception('Arguments not found (not set)');
		}

		// Copy list, so it can manipulated
		$args = $_SERVER['argv'];

		// Lose first argument: script itself
		array_shift($args);

		// Any arguments left?
		if (count($args) === 0) {
			throw new \Exception('Arguments not found (not given)');
		}

		// Set arguments, done
		$this->args = $args;
	}

}

new Crawler();
