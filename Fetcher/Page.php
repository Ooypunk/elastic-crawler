<?php

namespace ElasticCrawler\Fetcher;

/**
 * Page
 */
class Page {

	private $url;
	private $curl_info;
	private $curl_headers = [];
	private $curl_body;

	public function __construct($url) {
		$this->url = $url;
	}

	public function runCurl() {
		// create curl resource
		$ch = curl_init();

		// set url
		curl_setopt($ch, CURLOPT_URL, $this->url);

		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'parseHeaderLine'));

		// $output contains the output string
		$this->curl_body = curl_exec($ch);
		$this->curl_info = curl_getinfo($ch);

		// close curl resource to free up system resources 
		curl_close($ch);
	}

	public function parseHeaderLine($curl, $line) {
		$this->curl_headers[] = trim($line);
		return strlen($line);
	}

	public function getHeader() {
		return $this->curl_headers;
	}

	public function getBody() {
		return $this->curl_body;
	}

	public function isRedirect() {
		$code = $this->curl_info['http_code'];
		if ($code >= 300 && $code < 400) {
			return true;
		}
		foreach ($this->getHeader() as $line) {
			if (substr($line, 0, 8) === 'Refresh:') {
				return true;
			}
		}
		return false;
	}

}
