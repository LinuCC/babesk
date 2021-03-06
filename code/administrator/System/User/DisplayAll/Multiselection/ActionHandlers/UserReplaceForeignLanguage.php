<?php

namespace administrator\System\User\DisplayAll\Multiselection\Actions;

require_once __DIR__ . '/../Action.php';

class UserReplaceForeignLanguage extends Action {

	protected function execute($data) {

		if(isset($data['foreign_languages']) &&
				!isBlank($data['foreign_languages'])
			) {
			$this->languageApply(
				implode('|', $data['foreign_languages']),
				$data['_multiselectionSelectedOfUsers']
			);
		}
		else {
			$this->dieError('Keine Daten bekommen!');
		}
		$this->dieSuccess('Die Fremdsprachen wurden erfolgreich verändert.');
	}

	/**
	 * Applies the languages to the selected users
	 * @param  string $languages The new languages of the user
	 * @param  array $userIds   The ids of the users to change
	 */
	protected function languageApply($languages, $userIds) {

		$users = $this->_em
			->getRepository('DM:SystemUsers')
			->findById($userIds);
		foreach($users as $user) {
			$user->setForeignLanguage($languages);
			$this->_em->persist($user);
		}
		$this->_em->flush();
	}
}

?>