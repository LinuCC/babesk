<?php

require_once PATH_INCLUDE . '/phpExcel/PHPExcel.php';
require_once 'CctClass.php';

class SummaryOfClassesPdf {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public static function init ($interface) {
		self::$_interface = $interface;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public static function execute($startdate, $enddate) {
		$data = self::dataFetch();
		self::classesFill($data);
		self::tablesheetsCreate($startdate, $enddate);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches the data needed to create the Tables from the Database
	 * @param $classIds array () an array of Ids of classes to fetch
	 */
	protected static function dataFetch () {
		$query = 'SELECT CONCAT(u.forename, " ", u.name) AS userFullname,
				u.telephone AS telephone, u.ID AS userId,
				c.label AS classLabel, c.ID AS classId,
				CONCAT(g.gradelevel, g.label) AS grade,
				CONCAT(ct.forename, " ", ct.name) AS classteacherFullname,
				cu.name as unitName
			FROM KuwasysClasses c
				JOIN KuwasysUsersInClasses uic ON uic.ClassID = c.ID
				JOIN SystemUsers u ON uic.UserID = u.ID
				LEFT JOIN SystemUsersInGradesAndSchoolyears uigs
					ON uigs.UserID = u.ID
				LEFT JOIN SystemGrades g ON uigs.gradeId = g.ID
				LEFT JOIN jointClassTeacherInClass ctic ON ctic.ClassID = c.ID
				LEFT JOIN classTeacher ct ON ctic.ClassTeacherID = ct.ID
				LEFT JOIN KuwasysClassCategory cu ON cu.ID = c.unitId
			WHERE  uic.statusId = (SELECT ID FROM KuwasysUsersInClassStatuses WHERE KuwasysUsersInClassStatuses.name="active")
				AND uigs.schoolyearId = @activeSchoolyear
				AND c.schoolyearId = @activeSchoolyear;';
		try {
			$data = TableMng::query ($query);
		} catch (Exception $e) {
			self::$_interface->dieError ('Konnte die benötigten Daten nicht abrufen. Fehler:' . $e->getMessage ());
		}
		return $data;
	}

	/**
	 * Reorganizes the fetched data into CctClass-Instances; fills self::$classes
	 */
	protected static function classesFill ($data) {
		self::$classes = array ();
		foreach ($data as $row) {
			if ($class = CctClass::hasClassById ($row ['classId'], self::$classes)) {
				if (!$class->hasUser ($row ['userId'])) {
					$class->addUser ($row);
				}
			}
			else {
				$class = new CctClass ($row ['classId'], $row ['classLabel']);
				$class->addUser ($row);
				self::$classes [] = $class;
			}
			if (!$class->hasClassteacher ($row ['classteacherFullname'])) {
				$class->addClassteacher ($row ['classteacherFullname']);
			}
			if (!$class->hasUnitName ()) {
				$class->setUnitName ($row ['unitName']);
			}
		}
	}

	protected static function tablesheetsCreate($startdate, $enddate) {
		self::phpExcelInit ();
		foreach (self::$classes as $class) {
			$sheet = self::getNewSheet ();
			CctContent::fill ($sheet, $class, $startdate, $enddate);
		}
		self::phpExcelOut ();
	}

	/**
	 * Returns a new sheet-object
	 */
	protected static function getNewSheet () {
		if (self::$activeSheetNum !== NULL) {
			self::$activeSheetNum ++;
			self::$phpExcel->createSheet ();
		}
		else {
			self::$activeSheetNum = 0;
		}
		self::$phpExcel->setActiveSheetIndex (self::$activeSheetNum);
		return self::$phpExcel->getActiveSheet ();
	}

	/**
	 * Initializes the Process of creating a Excel-table
	 */
	protected static function phpExcelInit () {
		self::$phpExcel = new PHPExcel ();
		self::$phpExcel->getDefaultStyle()->getFont()->setSize(10);
		self::$activeSheetNum = NULL;
	}

	/**
	 * Writes the Excel-Tables and provide a download to the User
	 */
	protected static function phpExcelOut () {
		$phpExcelWriter = PHPExcel_IOFactory::createWriter(
			self::$phpExcel, "Excel2007");
		header('Content-Type: application/vnd.openxmlformats-officedocument.'.
      'spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Classes.xlsx"');
		header('Cache-Control: max-age=0');
		$phpExcelWriter->save ('php://output');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_interface;
	// protected $_languageManager;

	protected static $classes;
	protected static $tablesheets;

	protected static $phpExcel;
	protected static $activeSheetNum;
}

class CctContent {

	public static function fill (&$sheet, $class, $startdate, $enddate) {
		self::sheetPropertiesSet ($sheet, $class);
		self::headFill ($sheet, $class);
		self::mainHeadFill ($sheet, $class);
		self::occurDaysSet ($sheet, $class, $startdate, $enddate);
		self::mainFill ($sheet, $class);
		self::styleSet ($sheet, $class);
	}

	protected static function sheetPropertiesSet (&$sheet, $class) {
		$setup = $sheet->getPageSetup();
		//Fit the whole Sheet onto one page
		$setup->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$setup->setFitToWidth (1);
		$setup->setFitToHeight (1);
		$sheet->setTitle ('KursID ' . $class->getId ());
	}

	protected static function headFill (&$sheet, $class) {
		$sheet->mergeCells ('A1:Z1');
		$headline = sprintf ('Teilnehmerliste: %s; Kursleiter: %s', $class->getLabel (), $class->getClassteacherString ());
		$sheet->getCell ('A1')->setValue ($headline);
		$sheet->getStyle ('A1')->getFont ()->setBold (true)->setSize (15);
	}

	protected static function mainHeadFill (&$sheet, $class) {
		$sheet->getCell ('A3')->setValue ('Schülername');
		$sheet->getCell ('B3')->setValue ('Klasse');
		$sheet->getStyle ('B3')->applyFromArray (self::$rotateStyle);
		$sheet->getCell ('C3')->setValue ('Telefonnummer');
		$sheet->getCell ('D3')->setValue ('Datum   x=Anwesend   -=Abwesend   E=Entschuldigt F=Ferien/Frei');
		$sheet->mergeCells ('A3:A4');
		$sheet->mergeCells ('B3:B4');
		$sheet->mergeCells ('C3:C4');
		$sheet->mergeCells ('D3:Z3');
	}

	protected static function mainFill (&$sheet, $class) {
		$userRowCount = 0;
		foreach ($class->getUsers () as $user) {
			$userRowCount ++;
			$rowNum = $userRowCount + 4;
			$sheet->getCell ('A' . $rowNum)->setValue ($user ['userFullname']);
			$sheet->getCell ('B' . $rowNum)->setValue ($user ['grade']);
			$sheet->getCell ('C' . $rowNum)->setValue ($user ['telephone']);
			for ($i = 3; $i < 26; $i++) {
				$char = self::getCharAtNum ($i);
				$sheet->getCell ($char . $rowNum)->setValue (' ');
			}
			// $sheet->getRowDimension ($rowNum)->setRowHeight ();
		}
	}

	protected static function styleSet (&$sheet, $class) {
		$maxRow = $sheet->getHighestRow ();
		$maxColumn = $sheet->getHighestColumn ();
		for ($i = 0; $i <= $maxRow; $i++) {
			$sheet->getRowDimension ($i)->setRowHeight (20);
		}
		$sheet->getRowDimension (1)->setRowHeight (40);
		// $sheet->getRowDimension (2)->setRowHeight ();
		// $sheet->getRowDimension (3)->setRowHeight (30);
		$sheet->getRowDimension (4)->setRowHeight (35);
		$sheet->getColumnDimension ('A')->setWidth (25);
		$sheet->getColumnDimension ('B')->setWidth (4,5);
		$sheet->getColumnDimension ('C')->setWidth (18);
		for ($i = 3; $i < 26; $i++) {
			$char = self::getCharAtNum ($i);
			$sheet->getColumnDimension ($char)->setWidth (3);
		}
		$sheet->getStyle ('A3:Z' . $maxRow)->getBorders()->
			getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		// $sheet->getStyle ('D4:Z' . $maxRow)->getBorders()->
			// getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	}

	// protected static function occurDaysSet (&$sheet, $class) {
	// 	switch ($class->getUnitName ()) {
	// 		case 'monday':
	// 			$start = strtotime(date(self::$firstMon));
	// 			break;
	// 		case 'tuesday':
	// 			$start = strtotime(date(self::$firstMon) . "+1 day");
	// 			break;
	// 		case 'wednesday':
	// 			$start = strtotime(date(self::$firstMon) . "+2 day");
	// 			break;
	// 		case 'thursday':
	// 			$start = strtotime(date(self::$firstMon) . "+3 day");
	// 			break;
	// 	}
	// 	for ($i = 0; $i < 23; $i++) {
	// 		$ts = strtotime (date ('Y-m-d', $start) . sprintf('+%s week', $i));
	// 		$cell = self::getCharAtNum ($i + 3) . '4';
	// 		$sheet->getCell ($cell)->setValueExplicit (date ('d.m', $ts), PHPExcel_Cell_DataType::TYPE_STRING);
	// 		$sheet->getStyle ($cell)->applyFromArray (self::$rotateStyle);
	// 	}
	// }

	protected static function occurDaysSet(
		&$sheet, $class, $startdate, $enddate) {

		$dates = self::getAllOccurencesOfWeekdayBetween(
			$startdate, $enddate, $class->getUnitName());

		for($i = 0; $i < count($dates); $i++) {

			$shortDate = date('d.m', strtotime($dates[$i]));

			$cell = $sheet->getCell(self::getCharAtNum($i + 3) . '4');
			$cell->setValueExplicit(
				$shortDate, PHPExcel_Cell_DataType::TYPE_STRING);

			$cellStyle = $sheet->getStyle(self::getCharAtNum($i + 3) . '4');
			$cellStyle->applyFromArray(self::$rotateStyle);
		}
	}

	/**
	 * Searches all occurences of a Weekday between two dates
	 *
	 * @param  string $start   The Startdate
	 * @param  string $end     The Enddate
	 * @param  string $weekday The Weekday in English
	 * @return array           The occurences found as dates
	 */
	protected static function getAllOccurencesOfWeekdayBetween(
		$start, $end, $weekday) {

		$occurences = array();
		$dateIter = strtotime($start);
		$endTimestamp = strtotime($end);

		while($dateIter <= $endTimestamp) {
			$occurences[] = date('Y-m-d', $dateIter);
			$dateIter = strtotime("next $weekday", $dateIter);
		}

		return $occurences;
	}

	protected static $rotateStyle = array (
		'alignment' => array (
			'rotation' => 90,
			)
		);



	public static function getCharAtNum ($number) {
		return self::$numchar [$number % 51];
	}

	protected static $numchar = array (
		0 => 'A',
		1 => 'B',
		2 => 'C',
		3 => 'D',
		4 => 'E',
		5 => 'F',
		6 => 'G',
		7 => 'H',
		8 => 'I',
		9 => 'J',
		10 => 'K',
		11 => 'L',
		12 => 'M',
		13 => 'N',
		14 => 'O',
		15 => 'P',
		16 => 'Q',
		17 => 'R',
		18 => 'S',
		19 => 'T',
		20 => 'U',
		21 => 'V',
		22 => 'W',
		23 => 'X',
		24 => 'Y',
		25 => 'Z',
		26 => 'AA',
		27 => 'AB',
		28 => 'AC',
		29 => 'AD',
		30 => 'AE',
		31 => 'AF',
		32 => 'AG',
		33 => 'AH',
		34 => 'AI',
		35 => 'AJ',
		36 => 'AK',
		37 => 'AL',
		38 => 'AM',
		39 => 'AN',
		40 => 'AO',
		41 => 'AP',
		42 => 'AQ',
		43 => 'AR',
		44 => 'AS',
		45 => 'AT',
		46 => 'AU',
		47 => 'AV',
		48 => 'AW',
		49 => 'AX',
		50 => 'AY',
		51 => 'AZ',
		);

	protected static $firstMon = '2013-02-04';
}

?>
