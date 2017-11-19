<?php

namespace ElasticCrawler\Fetcher;

/**
 * Helper class for Page to make the extracting of data from the page header
 * easier
 *
 * @author Ooypunk
 */
class PageHeader {

	private $lines = [];

	public function addLine($line) {
		$this->lines[] = trim($line);
	}

	/**
	 * Get HTTP code (of page) from header.
	 * @return string|null HTTP code
	 */
	public function getHttpCode() {
		$matches = array();
		$pattern = '/HTTP\/1\.\d (\d{3})/';
		foreach ($this->lines as $line) {
			if (!preg_match($pattern, $line, $matches)) {
				continue;
			}
			return (int) $matches[1];
		}
	}

	/**
	 * Get content type (of page) from header.
	 * @return string|null Content type
	 */
	public function getContentType() {
		$matches = array();
		$pattern = '/Content-Type: ([a-z\/]*)[;\n]/';
		foreach ($this->lines as $line) {
			if (!preg_match($pattern, $line, $matches)) {
				continue;
			}
			return $matches[1];
		}
	}

}
