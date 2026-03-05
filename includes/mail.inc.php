<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../admin/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../admin/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../admin/PHPMailer/src/SMTP.php';

/**
 * Send an email using the project's shared SMTP settings.
 *
 * @param string $to      Recipient address
 * @param string $subject Subject line
 * @param string $body    Message body (HTML allowed)
 * @param string|null $altBody Plain‑text fallback body
 * @return bool           true on success, false on failure
 */
function sendMail(string $to, string $subject, string $body, ?string $altBody = null): bool
{
    $mail = new PHPMailer(true);
    try {
        // server configuration
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Enable verbose debug output
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'subodhpanthee09@gmail.com';
        // NOTE: this should be either the real account password or, preferably, an app-specific password.
        // the user previously had an app password stored in sendmail.php; the string below is kept for reference.
        $mail->Password   = 'Subodh@1234!';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // message configuration
        $mail->setFrom('noreply@bloodbank.com', 'Blood Bank');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        if ($altBody !== null) {
            $mail->AltBody = $altBody;
        }

        return $mail->send();
    } catch (Exception $e) {
        // log error for debugging
        error_log('Mail error: ' . $mail->ErrorInfo);
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        // store in session so caller can surface it
        $_SESSION['mail_error'] = $mail->ErrorInfo;
        return false;
    }
}
