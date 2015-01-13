<?php

namespace administrator\System\User\DisplayAll\Multiselection\Actions;

require_once __DIR__ . '/../Action.php';

class UserReplaceReligion extends Action {

	protected function execute($data) {

		if(isset($data['religion']) && !isBlank($data['religion'])) {
			$this->religionApply(
				implode('|', $data['religion']),
				$data['_multiselectionSelectedOfUsers']
			);
		}
		else {
			$this->dieError('Keine Daten bekommen!');
		}
		$this->dieSuccess('Die Religionen wurden erfolgreich verändert.');
	}

	protected function religionApply($religion, $userIds) {

		$users = $this->_em
			->getRepository('Babesk:SystemUsers')
			->findById($userIds);
		foreach($users as $user) {
			$user->setReligion($religion);
			$this->_em->persist($user);
		}
		$this->_em->flush();
	}
}

?>