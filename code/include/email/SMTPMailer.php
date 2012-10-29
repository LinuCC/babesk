<?php

require_once 'PHPMailer_5.2.1/class.phpmailer.php';
require_once PATH_ACCESS . 'GlobalSettingsManager.php';

/** This class is a wrapper for PHPMailer, for easy sending of mails
 * It uses the SMTP-technology to send the mails
 */
class SMTPMailer extends PHPMailer {
	public function SMTPMailer ($interface) {
		$this->PHPMailer ();
		$this->IsSMTP ();
		$this->_interface = $interface;
	}

	/** Loads an Email by the Xml-data of the given path of the Xml
	 *
	 */
	public function emailFromXmlLoad ($path) {
		$this->emailFromXmlInit ($path);
		$this->Subject = $this->emailFromXmlGetObj ('subject');
		$this->Body = $this->emailFromXmlGetObj ('body');
	}

	/** This function loads information for the Email-SMTP-Protocoll from the Database
	 * Looks for the smtp-Host, the Username and the Password for the Smtp-Server
	 * as well as the sender and the sendername.
	 */
	public function smtpDataFromDatabaseLoad () {

	}

	public function emailFromXmlInit ($path) {
		$this->_xmlEmail = simplexml_load_file($path);
		if (!$this->_xmlEmail) {
			$this->_interface->dieError ("Could not load the Xml of the Email!"
				. " Path:" . $path);
		}
	}

	private function emailFromXmlGetObj ($objName) {
		if(!is_object($this->_xmlEmail->$objName)) {
			$this->_interface->dieError ("Could not load an object of the email-Xml!" . " ObjectName:" . $objName);
		}
		return $this->_xmlEmail->$objName;
	}

	private $_xmlEmail;
	private $_interface;
}

?>