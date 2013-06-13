<?php

require_once 'CreateParticipationConfirmation.php';
require_once PATH_INCLUDE . '/email/SMTPMailer.php';

class UsersEmailParticipationConfirmation {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public static function init ($interface) {
		self::$_interface = $interface;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public static function execute () {
		if (isset ($_POST ['subject'], $_POST ['body'])) {
			self::send ();
		}
		else {
			self::form ();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Sends the Email
	 */
	protected static function send () {
		self::$_body = $_POST ['body'];
		self::$_subject = $_POST ['subject'];
		self::pdfCreate ();
		self::usersAddEmail ();
		self::emailsSend ();
		self::$_interface->showMsg ('Das senden der Emails wurde abgeschlossen');
		self::$_interface->dieDisplay ();
	}

	/**
	 * Shows a formular in which the User can write the Content of the Email
	 */
	protected static function form () {
		$_SESSION ['emailPcUserIds'] = $_POST ['userIds'];
		self::$_interface->showFormParticipationConfirmationEmail ();
	}

	protected static function pdfCreate () {
		CreateParticipationConfirmation::init (self::$_interface);
		self::$_usersWithPdf = CreateParticipationConfirmation::create ($_SESSION ['emailPcUserIds']);
	}

	/**
	 * Adds Email-Adresses to the Users
	 */
	protected static function usersAddEmail () {
		$userIds = array ();
		foreach (self::$_usersWithPdf as $user) {
			$userIds [] = $user->id;
		}
		self::emailAdressFetch ($userIds);
	}

	protected static function emailAdressFetch ($userIds) {
		$whereQuery = '';
		foreach ($userIds as $uid) {
			$whereQuery .= sprintf ('u.ID = "%s" OR ', $uid);
		}
		$whereQuery = rtrim($whereQuery, 'OR ');
		$query = sprintf (
			"SELECT u.ID AS userId, u.email AS userEmail
			FROM users u
			WHERE %s
			;", $whereQuery);
		try {
			$usersWithEmail = TableMng::query ($query, true);
		} catch (Exception $e) {
			self::$_interface->dieError ('Konnte die Email-Adressen der Benutzer nicht abrufen');
		}

		foreach (self::$_usersWithPdf as &$userPdf) {
			foreach ($usersWithEmail as $userMail) {
				if ($userPdf->id == $userMail ['userId']) {
					$userPdf->email = $userMail ['userEmail'];
				}
			}
		}
	}

	protected static function emailsSend () {
		foreach (self::$_usersWithPdf as $user) {
			if ($user->email != '') {
				$mailer = new SMTPMailer (self::$_interface);
				$mailer->smtpDataInDatabaseLoad ();
				$mailer->Subject = self::$_subject;
				$mailer->Body = self::$_body;
				$mailer->AddAddress($user->email);
				$mailer->AddAttachment ($user->participationConfirmationPath, 'Kurse.pdf');
				if ($mailer->Send ()) {
					self::$_interface->showMsg (sprintf('Die Email wurde erfolgreich an %s gesendet', $user->email));
				}
				else {
					self::$_interface->showError (sprintf('Die Email konnte nicht an %s gesendet werden. Weil: %s', $user->email, $mailer->ErrorInfo));
				}
			}
			else {
				self::$_interface->showMsg (sprintf(
					'An den Benutzer %s konnte keine Email gesendet werden da er keine Emailadresse angegeben hat.', $user->fullname));
			}
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_interface;

	protected static $_usersWithPdf;

	protected static $_subject;
	protected static $_body;
}

?>