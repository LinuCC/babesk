<?php

class KuwasysUsersCreateParticipationConfirmationPdf {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public static function init ($interface) {
		self::$_interface = $interface;
	}

	public static function execute ($gradeId, $userIds) {
		if(!count($userIds)) {
			self::$_interface->dieError('Keine Benutzer in dieser Klasse.');
		}
		$data = self::dataFetch ($gradeId, $userIds);
		self::usersFill ($data);
		$pdfPaths = self::pdfCreate ();
		self::pdfCombineAndOut ($pdfPaths);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected static function dataFetch ($gradeId, $userIds) {
		$whereQuery = '';
		foreach ($userIds as $uid) {
			$whereQuery .= sprintf ('u.ID = %s OR ', $uid);
		}
		$whereQuery = rtrim($whereQuery, 'OR ');
		$query = sprintf(
			'SELECT
				CONCAT(u.forename, " ", u.name) AS userFullname, u.ID as userId,
				sy.label AS schoolyear,
				c.label AS classLabel,
				cu.name AS unitName, cu.translatedName AS unitTranslatedName,
				uics.translatedName AS statusTranslatedName,
				CONCAT(g.gradelevel, g.label) AS gradeName,
				IF(
					c.ID, CONCAT(u.ID, "-", c.ID, "-", cu.ID),
					CONCAT(u.ID, "-")
				) AS grouper
			FROM SystemUsers u
				INNER JOIN SystemUsersInGradesAndSchoolyears uigs
					ON uigs.userId = u.ID
				INNER JOIN SystemSchoolyears sy ON sy.ID = uigs.schoolyearId
				INNER JOIN KuwasysUsersInClassesAndCategories uicc
					ON u.ID = uicc.UserID
				LEFT JOIN KuwasysUsersInClassStatuses uics ON uics.ID = uicc.statusId
				LEFT JOIN KuwasysClasses c
					ON c.ID = uicc.ClassID
					AND c.schoolyearId = @activeSchoolyear
				LEFT JOIN KuwasysClassesInCategories cic ON cic.classId = c.ID
				LEFT JOIN SystemGrades g ON g.ID = uigs.gradeId
				LEFT JOIN KuwasysClassCategories cu ON cic.categoryId = cu.ID
			WHERE uigs.schoolyearId = @activeSchoolyear
				AND (uics.ID IS NULL OR uics.name = "active")
				AND (uicc.UserID IS NULL OR uicc.categoryId = cic.categoryId)
				GROUP BY grouper
				ORDER BY g.gradelevel, g.label, cu.ID
			;', $gradeId);

		try {
			$data = TableMng::query ($query);
		} catch (MySQLVoidDataException $e) {
			self::$_interface->dieError ('Es wurden keine Schüler gefunden, für die man die Dokumente hätte erstellen können');
		} catch (Exception $e) {
			self::$_interface->dieError ('konnte die Daten der Schüler nicht abrufen' . $e->getMessage ());
		}
		return $data;
	}

	protected static function usersFill ($data) {
		foreach ($data as $row) {
			if (!$user = self::usersHas ($row ['userId'])) {
				$user = new UcpcPdfUser ($row ['userId'], $row ['userFullname'],
					$row ['schoolyear'], $row ['gradeName']);
				if(!empty($row ['classLabel'])) {
					$user->addClass ($row ['classLabel'], $row ['unitName'], $row ['unitTranslatedName'], $row ['statusTranslatedName']);
				}
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
		$pdfPaths = array ();
		$confTemplatePath = PATH_INCLUDE . '/pdf/printTemplates/printTemplateTest.html';
		if(!file_exists($confTemplatePath))
			$this->_interface->dieError($this->_languageManager->getText('errorPdfHtmlTemplateMissing') . $confTemplatePath);
		foreach (self::$_users as $user) {
			$pdf = new HtmlToPdfImporter ();
			$pdf->htmlImport ($confTemplatePath, true);
			$pdf->tempVarReplaceInHtml('fullname', $user->fullname, '#!%s#!');
			$pdf->tempVarReplaceInHtml('schoolyear', $user->schoolyear, '#!%s#!');
			$pdf->tempVarReplaceInHtml('grade', $user->grade, '#!%s#!');
			$pdf->tempVarReplaceInHtml('classList',
				self::pdfClassListCreate ($user), '#!%s#!');
			$pdf->htmlToPdfConvert();
			$pdfPaths [] = $pdf->pdfSaveTemporaryAndGetFilename ();
		}
		return $pdfPaths;
	}

	protected static function pdfClassListCreate ($user) {
		if(count($user->classes)) {
			$str = '<ul>';
			foreach ($user->classes as $unitTranslatedName => $unit) {
				$str .= sprintf ('<li>%s:<ul>', $unitTranslatedName);
				foreach ($unit as $class) {
					$str .= sprintf ('<li>%s</li>', $class->label);
				}
				$str .= '</ul>';
			}
			$str .= '</ul>';
		}
		else {
			$str = 'Dir wurden leider keine Kurse zugewiesen.';
		}
		return $str;
	}

	protected static function pdfCombineAndOut ($pdfPaths) {
		require_once PATH_INCLUDE . '/pdf/joinMultiplePdf.php';
		$pdfCombiner = new joinMultiplePdf ();
		foreach ($pdfPaths as $tmpPath) {
			$pdfCombiner->pdfAdd ($tmpPath);
		}
		$pdfCombiner->pdfCombine ();
		$pdfCombiner->pdfCombinedProvideAsDownload (self::$_pdfFilename);
		$pdfCombiner->pdfTempDirClean ();
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_interface;

	protected static $_users;

	protected static $_pdfFilename = 'userConfirmations.pdf';

}

class UcpcPdfUser {

	public function __construct ($id, $fullname, $schoolyear, $grade) {
		$this->id = $id;
		$this->fullname = $fullname;
		$this->schoolyear = $schoolyear;
		$this->grade = $grade;
	}

	public function addClass ($label, $unitName, $unitTranslatedName, $statusTranslatedName) {
		$this->classes [$unitTranslatedName] [] = new UcpcPdfClass ($label, $unitName, $unitTranslatedName, $statusTranslatedName);
	}

	public $id;
	public $fullname;
	public $schoolyear;
	public $classes;
	public $grade;

}

class UcpcPdfClass {

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
