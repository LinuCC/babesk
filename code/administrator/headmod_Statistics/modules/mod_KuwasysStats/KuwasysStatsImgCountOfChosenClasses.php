<?php

class KuwasysStatsImgCountOfChosenClasses {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public static function init($interface) {

		self::$_interface = $interface;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public static function execute() {

		self::dataFetch();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	public static function dataFetch() {

		try {
			$data = TableMng::query(
				'SELECT g.ID AS ID, g.gradeValue AS value,
				g.label AS label, uic.userCount AS userCount
				FROM grade g
				JOIN jointUsersInGrade uig ON g.ID = uig.gradeId
				JOIN (
					SELECT ID, userId, Count(*) AS userCount
					FROM jointUsersInClass
					GROUP BY userId
				)
				uic ON uig.userId = uic.userId
				', true);
			var_dump($data);


		} catch (MySQLVoidDataException $e) {
			self::$_interface->dieError(
				'Es konnten keine Daten gefunden werden');

		} catch (Exception $e) {
			$this->_interface->dieError(
				'Konnte die nötigen Daten nicht abrufen');
		}

		$gradelevels = self::gradelevelCalc($data);
	}

	public static function gradelevelCalc($data) {

		$gradelevel = array();

		foreach($data as $grade) {
			if(!isset($gradelevel[$grade['value']][$grade['label']])) {
				$gradelevel[$grade['value']][$grade['label']] = $grade['userCount'];
			}
			else {
				$gradelevel[$grade['value']][$grade['label']] += $grade['userCount'];
			}
		}

		return $gradelevel;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_interface;

}

?>