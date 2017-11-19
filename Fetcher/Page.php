<?php

namespace ElasticCrawler\Fetcher;

/**
 * Page
 * 
 * @author Ooypunk
 */
class Page {

	private $url;
	private $config;
	private $curl_info;
	private $curl_body;
	private $header;

	public function __construct($url) {
		$this->url = $url;
		$this->header = new PageHeader;

		# @todo Fill $config from a config file, or as argument in constructor
		$config = new \stdClass();
		$this->config = $config;
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
		if (isset($this->config->curl_user_agent)) {
			curl_setopt($ch, CURLOPT_USERAGENT, $this->config->curl_user_agent);
		}

		// $output contains the output string
		$this->curl_body = curl_exec($ch);
		$this->curl_info = curl_getinfo($ch);

		// close curl resource to free up system resources 
		curl_close($ch);
	}

	/**
	 * Helper function for runCurl(): trim given header line, then add it to
	 * list of header lines
	 * @param Resource $curl Curl instance
	 * @param string $line Header line
	 * @return int String length of header line
	 */
	public function parseHeaderLine($curl, $line) {
		$this->header->addLine($line);
		return strlen($line);
	}

	public function getBody() {
		return $this->curl_body;
	}

	public function getHttpCode() {
		return $this->header->getHttpCode();
	}

	public function getContentType() {
		return $this->header->getContentType();
	}

}
