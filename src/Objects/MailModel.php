<?php

namespace App\Objects;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailModel
{

	public function __construct()
	{
		$this->mail = new PHPMailer(true);
		//Server settings
		$this->mail->Host       = 'smtp.gmail.com';
		$this->mail->SMTPAuth   = true;
		$this->mail->Username   = 'email.no.reply.testing@gmail.com';
		$this->mail->Password   = 'noreplyEmail.com';
		$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$this->mail->Port       = 465;

		$this->mail->isSMTP();
		$this->mail->isHTML(true);
	}

	private function addRecipients($recipient) {
		$this->mail->setFrom('donotreply@gmail.com', 'do not reply');
		$this->mail->addAddress($recipient['email'], $recipient['name']);
	}

	public function sendEmail($recipient, $subject, $content, $attachment=false) {
		$this->addRecipients($recipient);

		$this->mail->Subject = $subject;
		$this->mail->Body = $content;

		if ($attachment) {
			$this->mail->AddStringAttachment($attachment['file'], $attachment['name']);
		}

		$this->mail->send();
	}
}