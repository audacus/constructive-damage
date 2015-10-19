<?php

namespace view;

use \Helper;
use \Config;
use controller\AbstractController;

abstract class AbstractView {

	const SCRIPTS_PATH = 'scripts';
	const DEFAULT_CSS_SCRIPTS_FOLDER = 'css';
	const DEFAULT_JS_SCRIPTS_FOLDER = 'js';
	const DEFAULT_SCRIPT_CONTENT = 'content.php';
	const DEFAULT_SCRIPT_HEADER = 'header.php';
	const DEFAULT_SCRIPT_FOOTER = 'footer.php';

	protected $config;
	protected $header;
	protected $content;
	protected $footer;
	protected $data;
	protected $cssFiles;
	protected $jsFiles;

	public function __construct() {
		$this->config = new Config(Helper::parseConfig(DEFAULT_CONFIG_PATH, CONFIG_PATH));
		$this->header = $this->setHeader();
		$this->footer = $this->setFooter();
		$this->content = $this->setContent();
		$this->setCssFiles(array_merge($this->getDefaultCssFiles(), $this->getViewCssFiles()));
		$this->setJsFiles(array_merge($this->getDefaultJsFiles(), $this->getViewJsFiles()));
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
		if (is_null($this->data)) {
			$this->setData(array());
		}
		return $this->data;
	}

	public function setData($data) {
		$this->data = $data;
		return $this->getData();
	}

	public function appendData($data) {
		$this->getData();
		array_push($this->data, $data);
		return $this->getData();
	}

	protected function getCssFiles() {
		if (is_null($this->cssFiles)) {
			$this->setCssFiles(array());
		}
		return $this->cssFiles;
	}

	protected function setCssFiles($cssFiles) {
		$this->cssFiles = $cssFiles;
		return $this->getCssFiles();
	}

	protected function appendCssFile($cssFile) {
		$this->getCssFiles();
		array_push($this->cssFiles, $cssFiles);
		return $this->getCssFiles();
	}

	protected function appendCssFiles($cssFiles) {
		foreach ($cssFiles as $cssFile) {
			$this->appendCssFile($cssFile);
		}
		return $this->getCssFiles();
	}

	protected function getJsFiles() {
		if (is_null($this->jsFiles)) {
			$this->setJsFiles(array());
		}
		return $this->jsFiles;
	}

	protected function setJsFiles($jsFiles) {
		$this->jsFiles = $jsFiles;
		return $this->getJsFiles();
	}

	protected function appendJsFile($jsFile) {
		$this->getJsFiles();
		array_push($this->jsFiles, $jsFiles);
		return $this->getJsFiles();
	}

	protected function appendJsFiles($jsFiles) {
		foreach ($jsFiles as $jsFile) {
			$this->appendJsFile($jsFile);
		}
		return $this->getJsFiles();
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
				Helper::getLowerCaseClassName($this),
				self::DEFAULT_SCRIPT_CONTENT
			);
			$this->content = new \SplFileInfo(implode(DIRECTORY_SEPARATOR, $contentPathParts));
		}
		return $this->content;
	}

	private function getDefaultFiles($folder, $fileEnding = null) {
		$defaultFilesPathParts = array(
			dirname(__FILE__),
			self::SCRIPTS_PATH,
			$folder,
			'*.'.(empty($fileEnding) ? $folder : $fileEnding)
		);
		return \Helper::getRelativePaths(glob(implode(DIRECTORY_SEPARATOR, $defaultFilesPathParts)));
	}

	private function getDefaultCssFiles() {
		return $this->getDefaultFiles(self::DEFAULT_CSS_SCRIPTS_FOLDER);
	}

	private function getDefaultJsFiles() {
		return $this->getDefaultFiles(self::DEFAULT_JS_SCRIPTS_FOLDER);
	}

	private function getViewFiles($folder, $fileEnding = null) {
		$viewFilesPathParts = array(
			dirname(__FILE__),
			self::SCRIPTS_PATH,
			Helper::getLowerCaseClassName($this),
			$folder,
			'*.'.(empty($fileEnding) ? $folder : $fileEnding)
		);
		return \Helper::getRelativePaths(glob(implode(DIRECTORY_SEPARATOR, $viewFilesPathParts)));

	}

	private function getViewCssFiles() {
		return $this->getViewFiles(self::DEFAULT_CSS_SCRIPTS_FOLDER);
	}

	private function getViewJsFiles() {
		return $this->getViewFiles(self::DEFAULT_JS_SCRIPTS_FOLDER);
	}
}