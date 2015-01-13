<?php

namespace Repository;

class Log extends \Doctrine\ORM\EntityRepository {

	public function log($msg, $severityStr, $categoryStr, $data) {

		$log = new \Babesk\ORM\SystemLog();
		$severity = $this->_em->getRepository('DM:SystemLogSeverity')
			->findOneByName($severityStr);
		if(!$severity) {
			$severity = new \Babesk\ORM\SystemLogSeverity();
			$severity->setName($severityStr);
		}
		$category = $this->_em->getRepository('DM:SystemLogCategory')
			->findOneByName($categoryStr);
		if(!$category) {
			$category = new \Babesk\ORM\SystemLogCategory();
			$category->setName($categoryStr);
		}
		$log->setMessage($msg);
		$log->setCategory($category);
		$log->setSeverity($severity);
		$log->setData(json_encode($data));
		$log->setDate(new \DateTime("now"));
		$this->_em->persist($severity);
		$this->_em->persist($category);
		$this->_em->persist($log);
		$this->_em->flush();
	}
}

?>