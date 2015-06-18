<?php

namespace Repository;

class User extends \Doctrine\ORM\EntityRepository {

	public function getGradeByUserAndSchoolyear($user, $schoolyear) {

		try {
			$query = $this->_em->createQuery(
				'SELECT g, uigs, sy FROM DM:SystemGrades g
				INNER JOIN g.attendances uigs
				INNER JOIN uigs.schoolyear sy WITH sy = :schoolyear
				WHERE uigs.user = :user
			');
			$query->setParameter('user', $user);
			$query->setParameter('schoolyear', $schoolyear);
			return $query->getOneOrNullResult();

		} catch (\Doctrine\ORM\NonUniqueResultException $e) {
			$this->_em->getRepository('DM:SystemLog')->log(
				'Found multiple active grades for user', 'Notice',
				'Babesk\ORM\User', array('userId' => $user->getId()));
			throw $e;
		}
	}


	public function getActiveGradeByUser($user) {

		try {
			$query = $this->_em->createQuery(
				'SELECT g, uigs, sy FROM DM:SystemGrades g
				INNER JOIN g.attendances uigs
				INNER JOIN uigs.schoolyear sy WITH sy.active = 1
				WHERE uigs.user = :user
			');
			$query->setParameter('user', $user);
			return $query->getOneOrNullResult();

		} catch (\Doctrine\ORM\NonUniqueResultException $e) {
			$this->_em->getRepository('DM:SystemLog')->log(
				'Found multiple active grades for user', 'Notice',
				'Babesk\ORM\User', array('userId' => $user->getId()));
			throw $e;
		}
	}
}

?>