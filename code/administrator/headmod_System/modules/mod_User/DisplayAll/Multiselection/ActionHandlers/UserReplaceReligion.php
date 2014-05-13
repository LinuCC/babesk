<?php

namespace administrator\System\User\DisplayAll\Multiselection\Actions;

require_once __DIR__ . '/../Action.php';

class UserReplaceReligion extends Action {

	public function actionExecute($data) {
		if(isset($data['religion']) && !isBlank($data['religion'])) {
			$this->religionApply(
				implode('|', $data['religion']),
				$data['_multiselectionSelectedOfUsers']
			);
		}
		$this->dieSuccess('Yaaaay!');
	}

	protected function religionApply($religion, $userIds) {
		require_once PATH_INCLUDE . '/orm-entities/SystemUsers.php';
		$users = $this->_entityManager
			->getRepository('\Babesk\ORM\SystemUsers')
			->findById($userIds);
		foreach($users as $user) {
			$user->setReligion($religion);
			$this->_entityManager->persist($user);
		}
		$this->_entityManager->flush();
	}
}

?>