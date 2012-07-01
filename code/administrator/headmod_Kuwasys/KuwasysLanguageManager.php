<?php

/**
 * Handles the XML
 * @author voelkerball
 *
 */
class KuwasysLanguageManager {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($interface, $pathToXml = NULL) {

		$this->_interface = $interface;
		$this->unsetModule();
		$this->initPathToXml($pathToXml);
		$this->loadXml();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * @param string $moduleName
	 * @throws Exception If there is no ModuleEntry with the given ModuleName
	 */
	public function setModule ($moduleName) {

		$this->_moduleName = $moduleName;

		foreach ($this->_simpleXmlObject as $xmlModule) {
			if ($xmlModule->name == $this->_moduleName) {
				$this->_xmlModuleObject = $xmlModule;
				return;
			}
		}
		throw new Exception('There is no Module-Entry for the given Module in the OutputText-XML:' . $moduleName);
	}

	public function unsetModule () {
		$this->_moduleName = NULL;
		$this->_xmlModuleObject = NULL;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function getText ($nameOfTextElement) {

		if(!isset($this->_xmlModuleObject)) {
			throw new BadMethodCallException('You need to set a Module first before using the getText()-function!');
		}
		if (!isset($this->_xmlModuleObject->text->$nameOfTextElement)) {
			$this->_interface->showError(sprintf('The Text-Element "%s" does not exist. Expect missing/wrong text!',
				$nameOfTextElement));
			return false;
		}
		return $this->_xmlModuleObject->text->$nameOfTextElement;
	}

	public function getTextOfModule ($nameOfTextElement, $moduleName) {

		foreach ($this->_simpleXmlObject as $xmlModule) {
			
			if ($xmlModule->name == $moduleName) {
				
				if (!isset($this->_simpleXmlObject->$moduleName->text->$nameOfTextElement)) {
					$this->_interface->showError(sprintf(
						'The Text-Element "%s" does not exist. Expect missing/wrong text!', $nameOfTextElement));
					return false;
				}
				return $this->_xmlModuleObject->text->$nameOfTextElement;

			}
		}
		throw new Exception('There is no Module-Entry for the given Module in the OutputText-XML:' . $moduleName);
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * @used-by KuwasysLanguageManager::__construct
	 */
	private function initPathToXml ($pathToXml) {

		if (isset($pathToXml)) {
			$this->_pathToXml = $pathToXml;
		}
		else {
			$this->_pathToXml = dirname(__FILE__) . '/gerOutputText.xml';
		}
	}
	private function loadXml () {

		$this->_simpleXmlObject = simplexml_load_file($this->_pathToXml);

		if (!$this->_simpleXmlObject) {
			$this->_interface->dieError(
				'Could not load XML for the KuwasysLanguageManager! Please Check the Path and name of the file:<br>' .
				$this->_pathToXml);
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * @var string
	 */
	protected $_pathToXml;

	/**
	 * @var SimpleXMLElement
	 */
	protected $_simpleXmlObject;

	/**
	 * @var AdminInterface
	 */
	protected $_interface;

	/**
	 * @var string
	 */
	protected $_moduleName;

	/**
	 * @var SimpleXMLElement
	 */
	protected $_xmlModuleObject;
}

?>