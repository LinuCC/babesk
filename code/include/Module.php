<?php

class Module {
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	protected $name;
	protected $relPath;
	protected $displayName;
	protected $executablePath;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name, $path) {
		$this->name = $name;
		$this->relPath = $path;
		$this->executablePath = $path . $this->name . '.php';
		$this->displayName = $display_name;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	public function getName () {
		return $this->name;
	}

	public function getDisplayName () {
		return $this->displayName;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute () {
		require $this->executablePath;
	}
}

?>