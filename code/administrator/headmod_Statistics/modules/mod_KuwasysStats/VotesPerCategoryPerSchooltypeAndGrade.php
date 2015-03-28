<?php

namespace administrator\Statistics\KuwasysStats;

require_once PATH_INCLUDE . '/Module.php';
require_once __DIR__ . '/KuwasysStats.php';
require_once PATH_3RD_PARTY . '/phpExcel/PHPExcel.php';

class VotesPerCategoryPerSchooltypeAndGrade extends \KuwasysStats {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$data = $this->gradesCountFetch();
		$schooltypesCount = $this->schooltypeVotesCount($data);
		$data = $this->dataRestructure($data, $schooltypesCount);
		$this->tableOutput($data);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
	}

	private function gradesCountFetch() {

		$stmt = $this->_pdo->query(
			'SELECT COUNT(*) countOfVotes, votedCategoryCount,
				CONCAT(g.gradelevel, g.label) AS gradeName,
				ss.name AS schooltypeName, userId
			FROM (
				SELECT userId, gradeId, COUNT(*) AS votedCategoryCount
				FROM (
					SELECT u.ID AS userId, uicc.categoryId AS categoryId,
						uigs.gradeId AS gradeId
					FROM SystemUsers u
					INNER JOIN SystemAttendances uigs
						ON uigs.userId = u.ID
						AND uigs.schoolyearId = @activeSchoolyear
					INNER JOIN KuwasysUsersInClassesAndCategories uicc
						ON uicc.UserID = u.ID
					INNER JOIN KuwasysUsersInClassStatuses uics
						ON uics.ID = uicc.statusId
						AND uics.name = "active"
					INNER JOIN KuwasysClasses c
						ON c.schoolyearId = @activeSchoolyear
						AND c.ID = uicc.classId
					GROUP BY uicc.categoryId, u.ID
				) uc
				GROUP BY uc.userId
			) vc
			INNER JOIN SystemGrades g ON g.ID = vc.gradeId
			INNER JOIN SystemSchooltypes ss ON ss.ID = g.schooltypeId
			GROUP BY vc.gradeId, vc.votedCategoryCount
			ORDER BY g.gradelevel, g.label
		');
		$res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		return $res;
	}

	/**
	 * Sums the votecount of the grades with the same schooltype
	 * @param  array  $data Containing the fetched grades with the votecount
	 * @return array        all of the schooltypes and the count
	 *                      [
	 *                          "<schooltypeName>" => [
	 *                              "<categoryCount>" => "<voteCount>"
	 *                          ]
	 *                      ]
	 */
	private function schooltypeVotesCount($data) {

		$schooltypesCount = array();
		foreach($data as $row) {
			$name = $row['schooltypeName'];
			$catCount = $row['votedCategoryCount'];
			if(isset($schooltypesCount[$name][$catCount])) {
				$schooltypesCount[$name][$catCount] += $row['countOfVotes'];
			}
			else {
				$schooltypesCount[$name][$catCount] = $row['countOfVotes'];
			}
		}
		foreach($schooltypesCount AS &$schooltype) {
			ksort($schooltype);
		}
		return $schooltypesCount;
	}

	/**
	 * Restructures the grade-data and aggregates the schooltypes into it
	 * @param  array  $data        the grade-rows fetched from the server
	 * @param  array  $schooltypes the schooltypes and their vote-count
	 * @return array               the restructured array allowing easy
	 *                             access to the data
	 *               Structure: [
	 *                   "<schooltype-/gradename" => [
	 *                       "<categoryCount>" => "<voteCount>"
	 *                   ]
	 *               ]
	 */
	private function dataRestructure($data, $schooltypes) {

		$sortedBySchooltypes = array();
		foreach($data as $row) {
			$sortedBySchooltypes[$row['schooltypeName']][] = $row;
		}
		$restructured = array();
		foreach($schooltypes as $schooltypeName => $schooltypeVoteCats) {
			//Make sure the Schooltype-row is at the beginning of its grades
			$restructured[$schooltypeName] = $schooltypeVoteCats;
			foreach($sortedBySchooltypes[$schooltypeName] as $gradeRow) {
				$count = $gradeRow['votedCategoryCount'];
				$name = $gradeRow['gradeName'];
				$restructured[$name][$count] = $gradeRow['countOfVotes'];
			}
		}
		return $restructured;
	}

	private function tableOutput($data) {

		$this->phpExcelInit();
		$this->tableHeaderSet();
		$this->tableFill($data);
		$this->tableOut();
	}

	private function phpExcelInit() {

		$this->_excel = new \PHPExcel();
		$this->_excel->setActiveSheetIndex(0);
	}

	private function tableHeaderSet() {

		$possibleCategoryCounts = array(1, 2, 3, 4);
		$sheet = $this->_excel->getActiveSheet();
		$sheet->mergeCells('A1:E1');
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
		$sheet->getRowDimension(1)->setRowHeight(30);
		$sheet->getColumnDimension('A')->setWidth(15);
		$sheet->setCellValueByColumnAndRow(
			0, 1, 'Teilnahme eines Schülers pro Wochentag gruppiert'
		);
		foreach($possibleCategoryCounts as $pcc) {
			$sheet->setCellValueByColumnAndRow(
				$pcc, 2, "Tag $pcc"
			);
		}
	}

	private function tableFill($data) {

		$possibleCategoryCounts = array(1, 2, 3, 4);
		$row = 3;
		$sheet = $this->_excel->getActiveSheet();
		foreach($data as $name => $categories) {
			$sheet->setCellValueByColumnAndRow(0, $row, $name);
			foreach($possibleCategoryCounts as $pcc) {
				$val = (isset($categories[$pcc])) ? $categories[$pcc] : 0;
				$sheet->setCellValueByColumnAndRow(
					0 + $pcc, $row, $val
				);
			}
			$row++;
		}
	}

	private function tableOut() {

		$writer = \PHPExcel_IOFactory::createWriter(
			$this->_excel, "Excel2007"
		);
		header(
			'Content-Type: application/vnd.openxmlformats-officedocument.' .
			'spreadsheetml.sheet'
		);
		header('Content-Disposition: attachment;filename="Classes.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	private $_excel;
}

?>