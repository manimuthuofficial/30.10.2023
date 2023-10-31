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
$mail->Password   = 'Protrack'; // Your protrack@mdsengg.com email password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable SSL encryption (Port 465)
$mail->Port       = 465; // SMTP Port for SSL encryption

// Set the sender (From) address and name
$mail->setFrom('protrack@mdsengg.com', 'ProTrack Sender');

// Recipients
$mail->addAddress('cadseat07@gmail.com', 'mdscad123'); // Replace with the recipient's email and name

// CC Recipients
$mail->addCC('manimuthuofficial@gmail.com', 'Kaviyan Manimuthu');

// Attachments
$mail->addAttachment('C:/Users/Mani/Downloads/1.jpeg', '1.jpeg'); // Replace with the path to your attachment

// Content
$mail->isHTML(true); // Set email format to HTML
$mail->Subject = 'Test Email From MDS Trackmate.';
$mail->Body    = 'MDS Trackmate - Subject';
$mail->AltBody = 'Test Email From MDS Trackmate';

// Send the email using PHPMailer
if ($mail->send()) 
{
    echo 'Email sent successfully';
	
	$imap = imap_open('{mail.mdsengg.com:993/imap/ssl}', 'protrack@mdsengg.com', 'Protrack');
	
	if ($imap) 
	{
        // Get the list of available mailboxes
        $mailboxes = imap_list($imap, '{mail.mdsengg.com:993/imap/ssl}', '*');
        $sentMailbox = null;

        // Find the "Sent" mailbox by checking for common names
        foreach ($mailboxes as $mailbox) 
		{
            if (strpos($mailbox, 'Sent') !== false) 
			{
                $sentMailbox = $mailbox;
                break;
            }
        }

        if ($sentMailbox) 
		{
            // Move the email to the "Sent" mailbox
            $mailData = $mail->getSentMIMEMessage();
            imap_append($imap, $sentMailbox, $mailData);

            // Close the IMAP connection
            imap_close($imap);
        } 
		else 
		{
            echo 'Could not find a suitable "Sent" mailbox.';
        }
    }
} 
else 
{
    echo 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
}


?>