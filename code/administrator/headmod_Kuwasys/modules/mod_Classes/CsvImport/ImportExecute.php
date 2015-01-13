<?php

namespace administrator\Kuwasys\Classes\CsvImport;

require_once 'CsvImport.php';


class ImportExecute extends \administrator\Kuwasys\Classes\CsvImport {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

			parent::entryPoint($dataContainer);

			$this->importData();
			// $query = $this->queryGenerate();
			// $this->queryExecute($query);
			$this->_interface->backlink('administrator|Kuwasys|Classes');
			$this->_interface->dieSuccess(
				_g('The Classes were successfully imported.')
			);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Imports the data of the classes
	 */
	private function importData() {

		if(!empty($_POST['classes'])) {
			$classes = $_POST['classes'];
		}
		else {
			$this->_interface->dieError(_g('No classes given.'));
		}

		$activeSchoolyear = $this->_em
			->getRepository('Babesk:SystemSchoolyears')
			->findOneByActive(true);

		foreach($classes as $classAr) {
			$classToAdd = new \Babesk\ORM\KuwasysClass();
			$classToAdd->setLabel($classAr['name'])
				->setDescription($classAr['description'])
				->setMaxRegistration($classAr['maxRegistration'])
				->setRegistrationEnabled($classAr['registrationEnabled'])
				->setSchoolyear($activeSchoolyear)
				->setIsOptional($classAr['isOptional']);
			//Add the classes
			$this->classteachersToClassAdd(
				$classToAdd, $classAr['classteacher']
			);
			//Add the categories
			foreach($classAr['categories'] as $categoryId) {
				$category = $this->_em->getReference(
					'Babesk:ClassCategory', $categoryId
				);
				$classToAdd->addCategory($category);
			}
			$this->_em->persist($classToAdd);
		}
		$this->_em->flush();
	}

	/**
	 * Adds the classteachers given in the array to the ORM-Object $class
	 * @param  Object $class              The Object representing the class
	 * @param  array  $classteachersArray The array containing the information
	 *                                    about the classteachers to be added
	 *                                    to $class
	 */
	private function classteachersToClassAdd($class, $classteachersArray) {

		foreach($classteachersArray as $ct) {
			$ctId = $ct['ID'];
			if($ctId == 'CREATE_NEW') {
				//Create a new classteacher
				$classteacher = new \Babesk\ORM\Classteacher();
				$ct['name'] = trim($ct['name']);
				$names = explode(' ', $ct['name'], 2);
				//If there was no space in the Classteachername, only add
				//surname
				$forename = (count($names) == 2) ? $names[0] : '';
				$surname = end($names);
				$classteacher->setName($surname)
					->setForename($forename)
					->setAddress('')
					->setTelephone('')
					->setEmail('');
				$class->addClassteacher($classteacher);
				$this->_em->persist($classteacher);
			}
			else if($ctId !== 0) {
				//Classteacher already exists, add him
				$classteacher = $this->_em->find(
					'Babesk:Classteacher', $ctId
				);
				$class->addClassteacher($classteacher);
			}
			else {
				//No classteacher assigned to class
			}
		}
	}



	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	private $_classes;
}
?>
