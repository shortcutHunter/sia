<?php

namespace App\Objects;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailModel
{

	public static function getModelByName($name)
	{
		$class_name = join("", array_map("ucfirst", explode("_", $name)));
        $class_name = "App\\Objects\\".$class_name;
        $model = new $class_name();
        return $model;
	}

	public function __construct()
	{
		$konfigurasi_obj = $this->getModelByName('konfigurasi');
		$konfigurasi = $konfigurasi_obj->first();

		$this->mail = new PHPMailer(true);
		//Server settings
		$this->mail->Host       = $konfigurasi->host;
		$this->mail->SMTPAuth   = true;
		$this->mail->Username   = $konfigurasi->username;
		$this->mail->Password   = $konfigurasi->password;
		$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$this->mail->Port       = $konfigurasi->port;

		$this->mail->isSMTP();
		$this->mail->isHTML(true);
	}

	private function addRecipients($recipient) {
		$this->mail->setFrom('donotreply@gmail.com', 'do not reply');
		$this->mail->addAddress($recipient['email'], $recipient['name']);
	}

	public function sendEmail($recipient, $subject, $content, $attachment=false) {

		$konfigurasi_obj = $this->getModelByName('konfigurasi');
		$konfigurasi = $konfigurasi_obj->first();

		if (!$konfigurasi->status_smtp) {
			return false;
		}

		$this->addRecipients($recipient);

		$this->mail->Subject = $subject;
		$this->mail->Body = $content;

		if ($attachment) {
			$this->mail->AddStringAttachment($attachment['file'], $attachment['name']);
		}

		try {
			$this->mail->send();
		} catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}
}