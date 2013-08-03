<?php

class CreateParticipationConfirmation {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public static function init ($interface, $pdfFilename = 'userConfirmations.pdf') {
		self::$_interface = $interface;
		self::$_pdfFilename = $pdfFilename;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Creates the participation-confirmations for each user
	 * @return array(CpcUsers) multiple Objects; each one represents one user
	 * with the PDF-File.
	 */
	public static function create ($userIds) {
		if (count ($userIds)) {
			$data = self::dataFetch ($userIds);
			self::usersFill ($data);
			self::pdfCreate ();
			return self::$_users;
		}
		else {
			throw new Exception ('No userIds given');
		}
	}


	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches the data for the users allowing to create the PDFs
	 */
	protected static function dataFetch ($userIds) {
		$whereQuery = '';
		foreach ($userIds as $uid) {
			$whereQuery .= sprintf ('u.ID = "%s" OR ', $uid);
		}
		$whereQuery = rtrim($whereQuery, 'OR ');
		$query = sprintf(
			'SELECT
				CONCAT(u.forename, " ", u.name) AS userFullname, u.ID as userId,
				sy.label AS schoolyear,
				c.label AS classLabel,
				cu.name AS unitName, cu.translatedName AS unitTranslatedName,
				uics.translatedName AS statusTranslatedName,
				CONCAT(g.gradelevel, g.label) AS gradeName
			FROM users u
				JOIN usersInGradesAndSchoolyears uigs ON uigs.userId = u.ID
					AND uigs.schoolyearId = @activeSchoolyear
				JOIN schoolYear sy ON sy.ID = uigs.schoolyearId
				JOIN jointUsersInClass uic ON u.ID = uic.UserID
				JOIN usersInClassStatus uics ON uics.ID = uic.statusId
				JOIN class c ON c.ID = uic.ClassID
				LEFT JOIN Grades g ON g.ID = uigs.gradeId
				LEFT JOIN kuwasysClassUnit cu ON c.unitId = cu.ID
			WHERE (%s) AND (uics.name = "active" OR uics.name = "waiting")
			;', $whereQuery);

		try {
			$data = TableMng::query ($query);
		} catch (MySQLVoidDataException $e) {
			self::$_interface->dieError ('Es konnten keine Dokumente erstellt werden; Möglicherweise hat sich keiner der Schüler angemeldet');
		} catch (Exception $e) {
			self::$_interface->dieError ('konnte die Daten der Schüler nicht abrufen' . $e->getMessage ());
		}
		return $data;
	}

	protected static function usersFill ($data) {
		foreach ($data as $row) {
			if (!$user = self::usersHas ($row ['userId'])) {
				$user = new CpcUser ($row ['userId'], $row ['userFullname'],
					$row ['schoolyear'], $row ['gradeName']);
				$user->addClass ($row ['classLabel'], $row ['unitName'], $row ['unitTranslatedName'], $row ['statusTranslatedName']);
				self::$_users [] = $user;
			}
			else {
				$user->addClass ($row ['classLabel'], $row ['unitName'], $row ['unitTranslatedName'], $row ['statusTranslatedName']);
			}
		}
	}

	protected static function usersHas ($userId) {
		if (isset (self::$_users)) {
			foreach (self::$_users as $user) {
				if ($user->id == $userId) {
					return $user;
				}
			}
		}
		return false;
	}

	protected static function pdfCreate () {
		require_once PATH_INCLUDE . '/pdf/HtmlToPdfImporter.php';
		$confTemplatePath = PATH_INCLUDE . '/pdf/printTemplates/printTemplateTest.html';
		if(!file_exists($confTemplatePath))
			$this->_interface->dieError($this->_languageManager->getText('errorPdfHtmlTemplateMissing') . $confTemplatePath);
		foreach (self::$_users as &$user) {
			$pdf = new HtmlToPdfImporter ();
			$pdf->htmlImport ($confTemplatePath, true);
			$pdf->tempVarReplaceInHtml('fullname', $user->fullname, '#!%s#!');
			$pdf->tempVarReplaceInHtml('schoolyear', $user->schoolyear, '#!%s#!');
			$pdf->tempVarReplaceInHtml('grade', $user->grade, '#!%s#!');
			$pdf->tempVarReplaceInHtml('classList',
				self::pdfClassListCreate ($user), '#!%s#!');
			$pdf->htmlToPdfConvert();
			$user->participationConfirmationPath = $pdf->pdfSaveTemporaryAndGetFilename ();
		}
	}

	protected static function pdfClassListCreate ($user) {
		$str = '<ul>';
		foreach ($user->classes as $unitTranslatedName => $unit) {
			$str .= sprintf ('<li>%s:<br /><ul>', $unitTranslatedName);
			foreach ($unit as $class) {
				$str .= sprintf ('<li>%s (%s)</li>', $class->label, $class->statusTranslatedName);
			}
			$str .= '</ul>';
		}
		$str .= '</ul>';
		return $str;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_interface;

	protected static $_users;

	protected static $_pdfFilename;

}

class CpcUser {
	public function __construct ($id, $fullname, $schoolyear, $grade) {
		$this->id = $id;
		$this->fullname = $fullname;
		$this->schoolyear = $schoolyear;
		$this->grade = $grade;
	}

	public function addClass ($label, $unitName, $unitTranslatedName, $statusTranslatedName) {
		$this->classes [$unitTranslatedName] [] = new CpcClass ($label, $unitName, $unitTranslatedName, $statusTranslatedName);
	}

	public $id;
	public $fullname;
	public $schoolyear;
	public $classes;
	public $grade;
	public $participationConfirmationPath;

}

class CpcClass {

	public function __construct ($label, $unitName, $unitTranslatedName, $statusTranslatedName) {
		$this->label = $label;
		$this->unitName = $unitName;
		$this->unitTranslatedName = $unitTranslatedName;
		$this->statusTranslatedName = $statusTranslatedName;
	}

	public $label;
	public $unitName;
	public $unitTranslatedName;
	public $statusTranslatedName;

}

?>
