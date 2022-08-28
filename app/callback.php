<?php
require_once "config.php";
require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_POST['email'];
$name = $_POST['name'];
$phone = $_POST['phone'];
$text = $_POST['text'];

$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';

$body = '<h3>'. $name .'<h3>
        <p>'. $email .'</p>
        <p>'. $phone .'</p>
        <p>'. $text .'</p>';

$mail->From = $config['mail'];
$mail->FromName = "Motor Garant";

$mail->addAddress($config['mail'], $name);

//Provide file path and name of the attachments
//$mail->addAttachment("server.txt", "File.txt");
//$mail->addAttachment("file.pdf", "file.pdf"); //Filename is optional

$mail->isHTML(true);

$mail->Subject = "Зворотній зв'язок";//'=?UTF-8?B?'.base64_encode('Зворотній зв'язок').'?='
$mail->Body = $body;
$mail->AltBody = $name . ' | ' . $email . ' | ' . $phone . ' | ' . $text;

if($email && $name && $phone){
    $result = $mail->send();

    if ($result) {
        echo json_encode(['response' => 'true']);
    } else {
        echo json_encode(['response' => 'false', 'error' => 'Помилка відправки пошти !']);
    }
} else {exit;}

?>