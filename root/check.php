<?php
session_start();
require_once '../app/config.php';
if($_POST['login'] && $_POST['password']){
    $p = md5(md5($_POST['password']));
    // echo $p;
    $pdo = new PDO("mysql:host=localhost:3306;dbname=".DBNAME.";charset=utf8;", USERNAME, PASSWORD);

    $sql = "SELECT id, login, password FROM auth WHERE login = :login AND password = :password";
    $stmt = $pdo->prepare($sql);
    $params = [
        ':login' => $_POST['login'],
        ':password' => $p
    ];
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if($result){
        $_SESSION['auth'] = true;
        header("Location:./");
    } else {
        $_SESSION['error'] = 'Не верный логин или пароль!';
        header("Location:./");
    }
    echo var_dump($result);
}
?>