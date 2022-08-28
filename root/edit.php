<?php
session_start();
require_once '../app/config.php';

if($_SESSION['auth']){
    if($_POST['name'] && $_POST['id'] && $_POST['type']){
    
        $pdo = new PDO("mysql:host=localhost:3306;dbname=".DBNAME.";charset=utf8;", USERNAME, PASSWORD);

        if($_POST['type'] == 'kasco'){
            
            $sql = "UPDATE kasco_tokens SET code=:code WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $params = [
                ':id' => $_POST['id'],
                ':code' => $_POST['name']
            ];
            $stmt->execute($params);
            
        } elseif ($_POST['type'] == 'civilian') {
            
            $sql = "UPDATE tokens SET code=:code, price=:price WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $params = [
                ':id' => $_POST['id'],
                ':code' => $_POST['name'],
                ':price' => $_POST['prices']
            ];
            $stmt->execute($params);
            
        }
    }
}

header("Location:./");
?>