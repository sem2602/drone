<?php

require_once('config.php');

//file_put_contents('server.txt', print_r($_SERVER, true));

// if($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']){
//     echo json_encode(['response' => false, 'error' => 'Помилка авторизації!!!']);
//     exit;
// }



$code = $_POST['code'];
$pdo = new PDO("mysql:host=localhost:3306;dbname=".DBNAME.";charset=utf8;", USERNAME, PASSWORD);

$sql = "SELECT id, code, hesh, status, price FROM tokens WHERE code = :code";
$stmt = $pdo->prepare($sql);
$params = [':code' => $code];
$stmt->execute($params);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM prices";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$prices = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pr = [];
foreach ($prices as $price){
    $pr[$price['id']] = $price['sum'];
}

if(empty($result)){
    echo json_encode(['response' => false, 'error' => 'Код в системі не знайдено!!!']);
    $pdo = NULL;
    exit;
}

if($result['status']){
    echo json_encode(['response' => false, 'error' => 'Даний код вже використаний!!!']);
    $pdo = NULL;
    exit;
}

$str = $code . $result['id'] . random_int(100, 10000);
$hesh = md5($str);

$sql = "UPDATE tokens SET hesh = :hesh WHERE id = :id";
$stmt = $pdo->prepare($sql);
$params = [':id' => $result['id'], ':hesh' => $hesh];
$stmt->execute($params);

$form = '<form id="form_civilian_add" onsubmit="return false">

<div class="modal-header">
  <h5 class="modal-title" id="staticBackdropLabel">Цивілка № - '. $result['code'] .'</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  
  <div class="mb-3">
    <label for="name-input" class="form-label">Експлуатант</label>
    <input type="text" class="form-control" name="name" placeholder="Введіть і\'мя та прізвище..." required>
  </div>

  <div class="mb-3">
    <label for="name-input" class="form-label">Свідоцтво про льотну придатність/Сертифікат</label>
    <input type="text" class="form-control" name="certificate" placeholder="Введіть номер свідотства..." required>
  </div>

  <div class="mb-3">
    <label for="phone-input" class="form-label">Телефон</label>
    <input type="tel" pattern="[0-9]{10}" class="form-control" id="phone-input" name="phone" placeholder="Введіть номер телефону..." required>
    </div>
  
    <div class="mb-3">
        <label for="email-input" class="form-label">Email</label>
        <input type="email" class="form-control" name="email" placeholder="name@example.com" required>
    </div>

    <div class="mb-3">
    <label for="name-input" class="form-label">Тип повітряного судна</label>
    <input type="text" class="form-control" name="air_type" placeholder="Введіть тип БПЛА..." required>
  </div>

  <div class="mb-3">
    <label for="name-input" class="form-label">Вартість</label>
    <input type="text" class="form-control" name="price" placeholder="" value="'.$pr[$result['price']].' грн." disabled readonly>
  </div>

  <div class="mb-3">
    <label for="name-input" class="form-label">Державний реєстраційний знак / номер БПЛА</label>
    <input type="text" class="form-control" name="air_number" placeholder="Введіть номер БПЛА..." required>
  </div>

  <div class="mb-3">
    <label for="name-input" class="form-label">Рік випуску</label>
    <input type="text" class="form-control" name="air_year" placeholder="Рік випуску..." required>
  </div>

  <div class="mb-3">
    <label for="name-input" class="form-label">Злітна маса</label>
    <input type="text" class="form-control" name="air_weight" placeholder="Злітна маса..." required>
  </div>

  <div class="mb-3">
    <label for="name-input" class="form-label">Види польотів</label>
    <input type="text" class="form-control" name="type_fly" placeholder="Види польотів..." required>
  </div>


</div>
<div class="modal-footer">        
  <button type="submit" class="btn btn-primary">Відправити</button>
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
</div>

</form>';

$result['response'] = true;
$result['hesh'] = $hesh;
$result['form'] = $form;

echo json_encode($result);

$pdo = NULL;


?>