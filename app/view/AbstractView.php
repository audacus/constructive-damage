<?php

namespace view;

use controller\AbstractController;

class AbstractView {

	const SCRIPTS_PATH = 'scripts';
	const DEFAULT_SCRIPT = 'index.php';
	const DEFAULT_SCRIPT_HEADER = 'header.php';
	const DEFAULT_SCRIPT_FOOTER = 'footer.php';

	protected $header;
	protected $content;
	protected $footer;
	protected $data;

	public function __construct() {
		$this->header = $this->setHeader();
		$this->footer = $this->setFooter();
		$this->content = $this->setContent();
	}

	public function getContent() {
		return file_get_contents($this->header).file_get_contents($this->content).file_get_contents($this->footer);
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
				strtolower(end(explode(BACKSLASH, get_class($this)))),
				self::DEFAULT_SCRIPT
			);
			$this->content = new \SplFileInfo(implode(DIRECTORY_SEPARATOR, $contentPathParts));
		}
		return $this->content;
	}
}