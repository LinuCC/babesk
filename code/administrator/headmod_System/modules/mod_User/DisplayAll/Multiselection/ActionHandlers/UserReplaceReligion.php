<?php

namespace administrator\System\User\DisplayAll\Multiselection\Actions;

require_once __DIR__ . '/../Action.php';

class UserReplaceReligion extends Action {

	public function actionExecute($data) {
		if(isset($data['religion']) && !isBlank($data['religion'])) {

		}
	}
}

?>