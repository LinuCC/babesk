<?php 

/**
 * This class stores the Translations of the Status of the joints between Users and Classes.
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
final class jointUserInClassStatusTranslation {
	
	public function __construct ($languageManager) {
		
		require_once PATH_ADMIN . '/headmod_Kuwasys/KuwasysLanguageManager.php';
		$this->_languageManager = $languageManager;
		
		$this->activeTrans = $this->_languageManager->getTextOfModule('jointUsersInClassStatusLabelActive', 'Classes');
		$this->waitingTrans = $this->_languageManager->getTextOfModule('jointUsersInClassStatusLabelWaiting', 'Classes');
		$this->request1Trans = $this->_languageManager->getTextOfModule('jointUsersInClassStatusLabelFirstRequest', 'Classes');
		$this->request2Trans = $this->_languageManager->getTextOfModule('jointUsersInClassStatusLabelSecondRequest', 'Classes');
		$this->request1 = 'request#1';
		$this->request2 = 'request#2';
		$this->waiting = 'waiting';
		$this->active = 'active';
	}
	
	public function getFirstRequest () {
		return $this->request1;
	}
	public function getSecondRequest () {
		return $this->request2;
	}
	public function getWaiting () {
		return $this->waiting;
	}
	public function getActive () {
		return $this->active;
	}
	
	public function statusArrayGet () {
		
		return array(
				array('name' => $this->request1, 'nameTrans' => $this->request1Trans),
				array('name' => $this->request2, 'nameTrans' => $this->request2Trans),
				array('name' => $this->waiting, 'nameTrans' => $this->waitingTrans),
				array('name' => $this->active, 'nameTrans' => $this->activeTrans),
				);
	}
	
	public function statusTranslate ($status) {
		
		$statusTrans = ';';
		switch($status) {
			case $this->request1:
				$statusTrans = $this->request1Trans;
				break;
			case $this->request2:
				$statusTrans = $this->request2Trans;
				break;
			case $this->waiting:
				$statusTrans = $this->waitingTrans;
				break;
			case $this->active:
				$statusTrans = $this->activeTrans;
				break;
			default:
				$statusTraans = 'status not found!';
		}
		return $statusTrans;
	}
	
	private $request1;
	private $request2;
	private $waiting;
	private $active;
	private $request1Trans;
	private $request2Trans;
	private $waitingTrans;
	private $activeTrans;
	
	/**
	 * @var KuwasysLanguageManager
	 */
	private $_languageManager;
}

?>