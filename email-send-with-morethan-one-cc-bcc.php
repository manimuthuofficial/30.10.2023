<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-6.7.1/src/Exception.php';
require 'PHPMailer-6.7.1/src/PHPMailer.php';
require 'PHPMailer-6.7.1/src/SMTP.php';

// Create a new PHPMailer instance
$mail = new PHPMailer();

// Server settings for mdsengg.com
$mail->isSMTP();
$mail->Host       = 'mail.mdsengg.com'; // SMTP server for mdsengg.com
$mail->SMTPAuth   = true;
$mail->Username   = 'protrack@mdsengg.com'; // Your protrack@mdsengg.com email address
$mail->Password   = 'xxxxx'; // Your protrack@mdsengg.com email password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable SSL encryption (Port 465)
$mail->Port       = 465; // SMTP Port for SSL encryption

// Set the sender (From) address and name
$mail->setFrom('protrack@mdsengg.com', 'ProTrack Sender');

// Recipients
$mail->addAddress('protrack@mdsengg.com', 'ProTrack Sender - Mani Setting'); // To

// CC Recipients
$mail->addCC('cc1@example.com', 'CC Recipient 1');
$mail->addCC('cc2@example.com', 'CC Recipient 2');

// BCC Recipients
$mail->addBCC('bcc1@example.com', 'BCC Recipient 1');
$mail->addBCC('bcc2@example.com', 'BCC Recipient 2');

// Content
$mail->isHTML(true); // Set email format to HTML
$mail->Subject = 'Test Email From MDS Trackmate.';
$mail->Body    = '11111';
$mail->AltBody = 'Test Email From MDS Trackmate - BODY';

// Send the email
if ($mail->send()) 
{
    echo 'Email sent successfully';
} 
else 
{
    echo 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
}

?>