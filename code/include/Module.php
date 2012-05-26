<?php

class Module {
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $name;
	private $path;
	private $displayName;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name, $path) {
		$this->name = $name;
		$this->path = $path;
		//$this->executablePath = $path . $this->name . '.php';
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