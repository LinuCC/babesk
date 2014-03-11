<?php

require_once 'KuwasysUsersCreateParticipationConfirmation.php';

class AssignUsersInClassParticipationConfirmation
	extends KuwasysUsersCreateParticipationConfirmationPdf{

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////


	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public static function execute ($userIds) {
		$data = self::dataFetch ($userIds);
		self::usersFill ($data);
		$pdfPaths = self::pdfCreate ();
		self::pdfCombineAndOut ($pdfPaths);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected static function dataFetch ($userIds) {
		$query =
			'SELECT
				CONCAT(u.forename, " ", u.name) AS userFullname, u.ID as userId, uic.statusId AS statusId,
				sy.label AS schoolyear,
				c.label AS classLabel,
				cu.name AS unitName, cu.translatedName AS unitTranslatedName,
				uics.translatedName AS statusTranslatedName,
				CONCAT(g.gradelevel, g.label) AS gradeName,
				IF(c.ID, CONCAT(u.ID, "-", c.ID), CONCAT(u.ID, "-")) AS grouper
			FROM users u
				JOIN usersInGradesAndSchoolyears uigs ON uigs.userId = u.ID
				JOIN SystemSchoolyear sy ON sy.ID = uigs.SchoolYearID
				JOIN KuwasysTemporaryRequestsAssign uic
					ON u.ID = uic.userId AND (
						uic.statusId = 1 OR uic.statusId = 0
					)
				LEFT JOIN usersInClassStatus uics ON uics.ID = uic.statusId
				LEFT JOIN KuwasysClasses c ON c.ID = uic.classId AND c.schoolyearId = @activeSchoolyear
				LEFT JOIN SystemGrades g ON g.ID = uigs.gradeId
				LEFT JOIN KuwasysClassCategory cu ON c.unitId = cu.ID
			WHERE uigs.schoolyearId = @activeSchoolyear
			GROUP BY grouper
			;';

		try {
			$data = TableMng::query ($query);
		} catch (MySQLVoidDataException $e) {
			self::$_interface->dieError ('Es wurden keine Schüler gefunden, für die man die Dokumente hätte drucken können');
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
				if(!empty($row ['classLabel']) && $row ['statusId'] != 0) {
					$user->addClass ($row ['classLabel'], $row ['unitName'], $row ['unitTranslatedName'], $row ['statusTranslatedName']);
				}
				self::$_users [] = $user;
			}
			else {
				if($row ['statusId'] != 0) {
					$user->addClass ($row ['classLabel'], $row ['unitName'], $row ['unitTranslatedName'], $row ['statusTranslatedName']);
				}
			}
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>
