<?php

require_once PATH_INCLUDE . '/exception_def.php';

/**
 * This class represents a row in a Csv-File
 */
class CsvRow {

	public function __construct () {
		$this->_elements = array();
	}

	/**
	 * Adds an Element to the row or replaces it if already existing
	 * @param $element string the element
	 * @param $attributeName string the name of the attribute. If not given, The
	 * attributeName will be a number
	 */
	public function elementSet ($element, $attributeName = false) {
		if ($attributeName) {
			$this->_elements [$attributeName] = $element;
		}
		else {
			$this->_elements [] = $element;
		}
	}

	/**
	 * Returns the element
	 * @return string
	 */
	public function elementGet ($attributeName) {
		if (!isset($this->_elements [$attributeName])) {
			throw new Exception (sprintf('AttributeName "%s" not found', $attributeName));
		}
		return $this->_elements [$attributeName];
	}

	/**
	 * Returns all elements of the Row
	 */
	public function elementGetAll () {
		return $this->_elements;
	}

	protected $_elements;
}

class CsvExporter {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////
	/**
	 * The Constructor
	 * @param $filePath string if given, the class will save the Temporary File
	 * to the filePath given. Else it will use the standard-directory
	 */
	public function __construct ($filePath = false) {
		$this->_filePath = self::$templateDir . '/TemporaryFile.csv';
		if ($filePath) {
			$this->_filePath = $filePath;
		}
		$this->fileOpen ($this->_filePath);
	}

	/////////////////////////////////////////////////////////////////////
	//Getters and Setters
	/////////////////////////////////////////////////////////////////////
	public function delimiterSet ($del) {
		$this->_delimiter = $del;
	}
	public function delimiterGet () {
		return $this->_delimiter;
	}
	public function enclosureSet ($enc) {
		$this->_elementEnclosure = $del;
	}
	public function enclosureGet () {
		return $this->_elementEnclosure;
	}
	public function filePathGet () {
		return $this->_filePath;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////
	/**
	 * Sets the attributes of the Csv-File to the elements in the row given
	 * @param $row CsvRow the attributes
	 */
	public function attributesSet ($row) {
		$this->_attributes = array ();
		foreach ($row->elementGetAll () as $element) {
			$this->_attributes [] = $element;
		}
	}

	/**
	 * Adds an attribute to the end of the attributes of the Csv-File
	 * @param $attrName string the Name of the Attribute
	 */
	public function attributeAdd ($attrName) {
		$this->_attributes [] = $attrName;
	}

	/**
	 * Adds a Row to the Csv-File, correctly sorting the elements to the attributes
	 * @param $row CsvRow the Row to add
	 */
	public function rowAdd ($row) {
		$this->elementsCheck($row->elementGetAll ());
		$this->_rows [] = $row;
	}

	/**
	 * Writes the Data into the Csv-File
	 */
	public function fileWrite () {
		$this->lineWrite ($this->_attributes);
		foreach ($this->_rows as $row) {
			$this->lineWrite ($this->rowParse ($row));
		}
	}

	/**
	 * Closes the File
	 */
	public function fileClose () {
		fclose ($this->_file);
	}

	/////////////////////////////////////////////////////////////////////
	//Implementations
	/////////////////////////////////////////////////////////////////////
	protected function fileOpen ($path) {
		$this->_file = fopen ($path, 'w');
		if (!$this->_file) {
			throw new CsvExportException (sprintf ('Could not open the file "%s" for writing'));
		}
	}

	protected function rowParse ($row) {
		$csvField = array ();
		foreach ($this->_attributes as $attr) {
			try {
				$element = $row->elementGet ($attr);
			} catch (Exception $e) {
				$element = '';
			}
			$csvField [] = $element;
		}
		return $csvField;
	}

	protected function lineWrite ($elements) {
		fputcsv ($this->_file, $elements, $this->_delimiter, $this->_elementEnclosure);
	}

	/**
	 * Checks if the attributes given in the elements are valid
	 */
	protected function elementsCheck ($elements) {
		foreach ($elements as $element) {
			$attr = key ($elements);
			if (!$this->attributeHas ($attr)) {
				throw new CsvExportException (sprintf ('Attribute "%s" not in the Attributes-list', $attr));
			}
		}
	}

	/**
	 * Checks if an attribute is in the attribute-list
	 */
	protected function attributeHas ($attrName) {
		foreach ($this->_attributes as $attr) {
			if ($attrName == $attr) {
				return true;
			}
		}
		return false;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	protected $_file;
	protected $_attributes;
	protected $_rows;
	protected $_delimiter = ';';
	protected $_elementEnclosure = '"';
	protected static $templateDir = PATH_INCLUDE;
	protected $_filePath;
}

?>