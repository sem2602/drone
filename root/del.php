<?php
session_start();
require_once '../app/config.php';
if($_SESSION['auth']){
    if($_POST['id'] && $_POST['type']){
        $pdo = new PDO("mysql:host=localhost:3306;dbname=".DBNAME.";charset=utf8;", USERNAME, PASSWORD);
        if($_POST['type'] == 'kasco'){
            $sql = "DELETE FROM kasco_tokens WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $params = [
                ':id' => $_POST['id']
            ];
            $stmt->execute($params);
        } elseif ($_POST['type'] == 'civilian') {
            $sql = "DELETE FROM tokens WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $params = [
                ':id' => $_POST['id']
            ];
            $stmt->execute($params);
        } else {}
    }
}
header("Location:./");
?>