<?php

namespace view;

use \Helper;
use \Config;
use controller\AbstractController;

class AbstractView {

	const SCRIPTS_PATH = 'scripts';
	const DEFAULT_SCRIPT_CONTENT = 'content.php';
	const DEFAULT_SCRIPT_HEADER = 'header.php';
	const DEFAULT_SCRIPT_FOOTER = 'footer.php';

	protected $config;
	protected $header;
	protected $content;
	protected $footer;
	protected $data;

	public function __construct() {
		$this->config = new Config(Helper::parseConfig(DEFAULT_CONFIG_PATH, CONFIG_PATH));
		$this->header = $this->setHeader();
		$this->footer = $this->setFooter();
		$this->content = $this->setContent();
	}

	public function getSiteContent() {
		$siteContent = null;
		ob_start();
		include_once($this->header);
		include_once($this->content);
		include_once($this->footer);
		$siteContent = ob_get_contents();
		ob_end_clean();
		return $siteContent;
	}

	public function getData() {
		return $this->data;
	}

	public function setData($data) {
		$this->data = $data;
		return $this->data;
	}

	private function setHeader() {
		$headerPathParts = array(
			dirname(__FILE__),
			self::SCRIPTS_PATH,
			self::DEFAULT_SCRIPT_HEADER
		);
		$this->header = new \SplFileInfo(implode(DIRECTORY_SEPARATOR, $headerPathParts));
		return $this->header;
	}

	private function setFooter() {
		$footerPathParts = array(
			dirname(__FILE__),
			self::SCRIPTS_PATH,
			self::DEFAULT_SCRIPT_FOOTER
		);
		$this->footer = new \SplFileInfo(implode(DIRECTORY_SEPARATOR, $footerPathParts));
		return $this->footer;
	}

	private function setContent($contentPath = null) {
		if (!empty($contentPath)) {
			$this->content = new \SplFileInfo($contentPath);
		} else {
			$contentPathParts = array(
				dirname(__FILE__),
				self::SCRIPTS_PATH,
				strtolower(end(explode('\\', get_class($this)))),
				self::DEFAULT_SCRIPT_CONTENT
			);
			$this->content = new \SplFileInfo(implode(DIRECTORY_SEPARATOR, $contentPathParts));
		}
		return $this->content;
	}
}