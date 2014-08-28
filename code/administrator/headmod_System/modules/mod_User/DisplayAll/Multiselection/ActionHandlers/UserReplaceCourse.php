<?php

namespace administrator\System\User\DisplayAll\Multiselection\Actions;

require_once __DIR__ . '/../Action.php';

class UserReplaceCourse extends Action {

	protected function execute($data) {

		if(isset($data['courses']) && !isBlank($data['courses'])) {
			$this->coursesApply(
				implode('|', $data['courses']),
				$data['_multiselectionSelectedOfUsers']
			);
		}
		else {
			$this->dieError('Keine Daten bekommen');
		}
		$this->dieSuccess('Die Kurse wurden erfolgreich verändert.');
	}

	protected function coursesApply($courses, $userIds) {

		$users = $this->_entityManager
			->getRepository('Babesk:SystemUsers')
			->findById($userIds);
		foreach($users as $user) {
			$user->setSpecialCourse($courses);
			$this->_entityManager->persist($user);
		}
		$this->_entityManager->flush();
	}
}

?>