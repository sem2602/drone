<?php

require_once('config.php');
require_once '../vendor/autoload.php';
use \Convertio\Convertio;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(empty($_POST['hesh']) || empty($_POST['email'])){
    echo json_encode(['response' => false, 'error' => 'Помилка данних форми !']);
    exit;
}

$pdo = new PDO("mysql:host=localhost:3306;dbname=".DBNAME.";charset=utf8;", USERNAME, PASSWORD);

$sql = "SELECT id, code, hesh, status FROM kasco_tokens WHERE hesh = :hesh";
$stmt = $pdo->prepare($sql);
$params = [':hesh' => $_POST['hesh']];
$stmt->execute($params);
        
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$id = $result['id'];

if(empty($result)){
    echo json_encode(['response' => false, 'error' => 'Не вірний хеш авторизації !']);
    $pdo = NULL;
    exit;
}

$_monthsList = array("01" => "січня", "02" => "лютого", 
"03" => "березня", "04" => "квітня", "05" => "травня", "06" => "червня", 
"07" => "липня", "08" => "серпня", "09" => "вересня",
"10" => "жовтня", "11" => "листопада", "12" => "грудня");

$date = date('d');//"Y-m-d H:i:s"
$year = date('Y');
$cur_date = date("Y-m-d");
$edate = strtotime($cur_date.'+ 1 years - 1 days');
$ed = date("d", $edate);
$em = date("m", $edate);
$ey = date("Y", $edate);


$month = $_monthsList[date('m')];

//GENERATE DOCUMENT
$findStr = ['{DOGOVOR}', '{DATE}', '{MONTH}', '{YEAR}', '{COMPANY}', '{NAME}', '{TYPE}', '{PRICE}', '{AIR_NUMBER}', '{YEAR}', '{WEIGHT}', '{FLY_TYPES}', '{PERCENT}', '{D}', '{M}', '{Y}', '{ED}', '{EM}', '{EY}'];

$percent = (int)$_POST['price'] * 0.12;

$replStr = [
        $result['code'],
        $date, $month, $year,
        $config['company'],
        $_POST['name'],
        $_POST['air_type'],
        $_POST['price'],
        $_POST['air_number'],
        $_POST['air_year'],
        $_POST['air_weight'],
        $_POST['type_fly'],
        $percent,
        $date, date('m'), $year,
        $ed, $em, $ey
        
    ];

$file = file_get_contents('templates/document2.xml');

$file = str_replace($findStr, $replStr, $file);

file_put_contents('document2.xml', $file);

$zip = new ZipArchive();
$zip->open('doc2.docx');
$zip->addFile("document2.xml", "word/document.xml");
$zip->close();

//MAKE PDF
$API = new Convertio($config['api']);// You can obtain API Key here: https://convertio.co/api/
$API->start('./doc2.docx', 'pdf')->wait()->download('./pdf/kasco_'. $id .'.pdf')->delete();


//SAVE NEW ORDER
$sql = "INSERT INTO orders (code, type, name, email, phone, price) VALUES (:code, :type, :name, :email, :phone, :price)";
$stmt = $pdo->prepare($sql);
$params = [
    ':code' => $result['code'],
    ':type' => 'Каско',
    ':name' => $_POST['name'],
    ':email' => $_POST['email'],
    ':phone' => $_POST['phone'],
    ':price' => $_POST['price']
];
$stmt->execute($params);

//SEND email to company
$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';

$body = '<h3>Сертифікат оформлений на користувача:<h3>
        <p>'.$_POST['name'].'</p>
        <p>'.$_POST['email'].'</p>
        <p>'.$_POST['phone'].'</p>';
        
$mail->From = $config['mail'];
$mail->FromName = "Motor Garant";
$mail->addAddress($config['mail'], "Motor Garant");
$mail->addAttachment("pdf/kasco_".$id.".pdf", "Сертифікат.pdf"); //Filename is optional

$mail->isHTML(true);
$mail->Subject = "Сертифікат Каско - " . $config['company'];
$mail->Body = $body;
$mail->AltBody = 'документи во вложении';

$res_mail_c = $mail->send();
if(!$res_mail_c){
    file_put_contents('mail_error.txt', print_r($res_mail_c, true));
}
$mail = NULL;

//SEND email to client
$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';

$body = '<h3>Сертифікат оформлений !<h3>
        <p>документи у вкладенні</p>
        <p></p>';
        
$mail->From = $config['mail'];
$mail->FromName = "Motor Garant";
$mail->addAddress($_POST['email'], "Motor Garant");
$mail->addAttachment("pdf/kasco_".$id.".pdf", "Сертифікат.pdf"); //Filename is optional
$mail->addAttachment("pdf/memo.pdf", "Памятка.pdf");
$mail->addAttachment("pdf/exclusion_cases_kasco.pdf", "Виключення із страхових випадків.pdf");

$mail->isHTML(true);
$mail->Subject = "Сертифікат Каско - " . $config['company'];
$mail->Body = $body;
$mail->AltBody = 'документи во вложении';

if($_POST['email']){
    $res_mail = $mail->send();

    if ($res_mail) {
        echo json_encode(['response' => 'true']);
        
        $sql = "UPDATE kasco_tokens SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $params = [':id' => $result['id'], ':status' => 1];
        $stmt->execute($params);
        
    } else {
        echo json_encode(['response' => 'false', 'error' => 'Помилка відправки пошти !']);
    }
} else {
    $pdo = NULL;
    exit;
}
$mail = NULL;
$pdo = NULL;
?>