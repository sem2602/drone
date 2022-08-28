<?php
session_start();
require_once '../app/config.php';

if($_SESSION['auth']){
    if($_POST['type'] && $_POST['name']){
        $pdo = new PDO("mysql:host=localhost:3306;dbname=".DBNAME.";charset=utf8;", USERNAME, PASSWORD);

        if($_POST['type'] == 'kasco'){
            $sql = "INSERT INTO kasco_tokens (code) VALUES (:code)";
            $stmt = $pdo->prepare($sql);
            $params = [
                ':code' => $_POST['name']
            ];
            $stmt->execute($params);
            
        } elseif ($_POST['type'] == 'civilian') {
            $sql = "SELECT * FROM prices WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $params = [':id' => $_POST['prices']];
            $stmt->execute($params);
            $price = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "INSERT INTO tokens (code, price) VALUES (:code, :price)";
            $stmt = $pdo->prepare($sql);
            $params = [
                ':code' => $_POST['name'],
                ':price' => $price['id']
            ];
            $stmt->execute($params);
            
        }
    }
}
header("Location:./");
?>