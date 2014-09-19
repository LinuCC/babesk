<?php

namespace Repository;

class User extends \Doctrine\ORM\EntityRepository {

	public function getActiveGradeByUser($user) {

		$query = $this->_em->createQuery(
			'SELECT g, uigs, sy FROM Babesk:SystemGrades g
			INNER JOIN g.usersInGradesAndSchoolyears uigs
			INNER JOIN uigs.schoolyear sy WITH sy.active = 1
			WHERE uigs.user = :user
		');
		$query->setParameter('user', $user);
		return $query->getOneOrNullResult();
	}
}

?>