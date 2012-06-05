<?php

require_once 'InstallationComponent.php';

class InstallationComponentManager {
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_components;
	private $_xmlPath;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct () {

		$this->_xmlPath = __DIR__ . '/InstallationComponents.xml';
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	public function getComponents () {

		return $this->_components;
	}

	public function setXMLPath ($xmlPath) {

		$this->_xmlPath = $xmlPath;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * @param InstallationComponent $component
	 */
	public function addComponent ($component) {

		$this->_components [] = $component;
	}

	public function loadComponentsFromXML () {

		$xmlComponents = simplexml_load_file($this->_xmlPath);

		if (!$xmlComponents) {
			die('Error loading the "InstallationComponents.xml"');
		}

		foreach ($xmlComponents->Component as $xmlComponent) {

			if (file_exists(__DIR__ . '/' . $xmlComponent->path)) {
				include_once $xmlComponent->path;
				$className = (string) $xmlComponent->className;
				if (class_exists($className)) {
					$this->_components [] = new $className($xmlComponent->name, $xmlComponent->nameDisplay, $xmlComponent->path);
				}
				else {
					echo 'Could not load class for ' . $xmlComponent->name;
				}
			}
			else {
				echo 'Could not load file for ' . $xmlComponent->name;
			}
		}
	}

	public function executeComponent ($name) {
		
		foreach ($this->_components as $component) {

			if ($component->getName() == $name) {
				$component->execute();
			}
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
}

?>