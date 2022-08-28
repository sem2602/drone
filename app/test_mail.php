<?php
require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



$mail = new PHPMailer();



$mail->From = "from@yourdomain.com";
$mail->FromName = "Full Name";

$mail->addAddress("sem2602@gmail.com", "Recipient Name");

//Provide file path and name of the attachments
$mail->addAttachment("server.txt", "File.txt");
$mail->addAttachment("file.pdf", "file.pdf"); //Filename is optional

$mail->isHTML(true);

$mail->Subject = "Subject Text";
$mail->Body = "<i>Mail body in HTML</i>";
$mail->AltBody = "This is the plain text version of the email content";



$result = $mail->send();

echo var_dump($result);
exit;


// try {
// ⠀⠀⠀⠀$mail->send();
// ⠀⠀⠀⠀echo "Message has been sent successfully";
// } catch (Exception $e) {
// ⠀⠀⠀⠀echo "Mailer Error: " . $mail->ErrorInfo;
// }






