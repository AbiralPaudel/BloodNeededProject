<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// This script is no longer used by the application.  It demonstrates how to
// call the shared helper if you need to send a one‑off message from the CLI
// or a simple form.  Credentials and other settings are centralized in
// includes/mail.inc.php.

require_once __DIR__ . "/../includes/mail.inc.php";

// example usage:
$to      = 'recipient@example.com';
$subject = 'Test message';
$body    = '<p>This is a test message sent from the blood bank system.</p>';

if (sendMail($to, $subject, $body)) {
    echo 'Message has been sent';
} else {
    echo 'Message could not be sent';
}
?>