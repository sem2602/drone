<?php

session_start();

require_once '../app/config.php';

if($_SESSION['auth']){
    
    $pdo = new PDO("mysql:host=localhost:3306;dbname=".DBNAME.";charset=utf8;", USERNAME, PASSWORD);

    $sql = "SELECT id, code, status FROM kasco_tokens";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $kasco = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $sql = "SELECT id, code, status, price FROM tokens";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $civilian = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $sql = "SELECT * FROM prices";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $prices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pr = [];
    foreach ($prices as $price){
        $pr[$price['id']] = $price['sum'];
    }
    
    $sql = "SELECT * FROM orders ORDER BY datetime DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    
  <title>Панель управления</title>
</head>
<body>

<?php if($_SESSION['auth']): ?>


    
    <nav class="navbar fixed-top navbar-light bg-light">
        <div class="container-fluid justify-content-evenly">
            
            <!--<img src="../img/logo.png" alt="logo" style="width:100px">-->
    
            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modal-add" onclick="add('kasco')">Добавить код Каско</button>
            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modal-add" onclick="add('civilian')">Добавить код Цивилки</button>
    
        </div>
    </nav>

	<div class="container mt-4 pt-4">
	    
	    
	    
	    
	    <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-codes-tab" data-bs-toggle="tab" data-bs-target="#nav-codes" type="button" role="tab" aria-controls="nav-codes" aria-selected="true">Кода сертификатов</button>
            <button class="nav-link" id="nav-orders-tab" data-bs-toggle="tab" data-bs-target="#nav-orders" type="button" role="tab" aria-controls="nav-orders" aria-selected="false">Сделки</button>
            
          </div>
        </nav>
        
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-codes" role="tabpanel" aria-labelledby="nav-codes-tab">
                  
                
                <div class="d-flex justify-content-evenly flex-wrap">
	        
	        <div class="">
	        
	            <?php if(empty($kasco)): ?>
                <h2>Нет номеров...</h2>
                <?php else: ?>
                
                <h2>Каско</h2>
                
                <table class="table table-bordered table-striped">
                    
                    <thead>
                        <span hidden>kasco</span>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Код</th>
                          <th scope="col">Статус</th>
                          <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php $codes_kasco = []; ?>
                        <?php foreach ($kasco as $value): ?>
                            <?php $codes_kasco[] = $value['code']; ?>
                            <tr>
                              <th scope="row"><?= $i ?><span hidden><?= $value['id'] ?></span></th>
                              <td><?= $value['code'] ?></td>
                              
                              <?php if($value['status']): ?>
                              <td class="text-center">использован</td>
                              <?php else: ?>
                              <td class="text-success text-center">OK</td>
                              <?php endif; ?>
                              
                              <td class="p-1">
                                <button type="button" class="btn btn-secondary btn-sm p-1" data-bs-toggle="modal" data-bs-target="#modal-edit" onclick="edit(this, 'kasco');"><img src="../img/edit.png" style="width:20px"></button>
                                <button type="button" class="btn btn-secondary btn-sm p-1" data-bs-toggle="modal" data-bs-target="#modal-del" onclick="del(this, 'kasco');"><img src="../img/delete.png" style="width:20px"></button>
                              </td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
	        
	        </div>
	    
	        <div class="">
	        
	            <?php if(empty($civilian)): ?>
                <h2>Нет номеров...</h2>
                <?php else: ?>
                
                <h2>Цивілка</h2>
                
                <table class="table table-bordered table-striped">
                    <span hidden>civilian</span>
                    <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Код</th>
                          <th scope="col">Статус</th>
                          <th scope="col">Цена</th>
                          <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php $codes_civilian = []; ?>
                        <?php foreach ($civilian as $value): ?>
                            <?php $codes_civilian[] = $value['code']; ?>
                            <tr>
                              <th scope="row"><?= $i ?><span hidden><?= $value['id'] ?></span></th>
                              <td><?= $value['code'] ?></td>
                              
                              <?php if($value['status']): ?>
                              <td class="text-center">использован</td>
                              <?php else: ?>
                              <td class="text-success text-center">OK</td>
                              <?php endif; ?>
                              
                              <td><?= $pr[$value['price']] ?></td>
                              
                              <td class="p-1">
                                <button type="button" class="btn btn-secondary btn-sm p-1" data-bs-toggle="modal" data-bs-target="#modal-edit" onclick="edit(this, 'civilian');"><img src="../img/edit.png" style="width:20px"></button>
                                <button type="button" class="btn btn-secondary btn-sm p-1" data-bs-toggle="modal" data-bs-target="#modal-del" onclick="del(this, 'civilian');"><img src="../img/delete.png" style="width:20px"></button>
                              </td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
	        
	        </div>
	        
	    </div>
                
                  
            </div>
            
            <div class="tab-pane fade" id="nav-orders" role="tabpanel" aria-labelledby="nav-orders-tab">
              
              
                <div class="">
	        
	            <?php if(empty($orders)): ?>
                <h2>Нет заказов...</h2>
                <?php else: ?>
                
               
                
                <table class="table table-bordered table-striped">
                    
                    <thead>
                        <span hidden>kasco</span>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Код</th>
                          <th scope="col">Тип</th>
                          <th scope="col">Цена</th>
                          <th scope="col">Имя</th>
                          <th scope="col">Почта</th>
                          <th scope="col">Телефон</th>
                          <th scope="col">Дата и время</th>
                          <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        
                        <?php foreach ($orders as $value): ?>
                            
                            <tr>
                              <th scope="row"><?= $i ?></th>
                              <td><?= $value['code'] ?></td>
                              <td><?= $value['type'] ?></td>
                              <td><?= $value['price'] ?></td>
                              <td><?= $value['name'] ?></td>
                              <td><?= $value['email'] ?></td>
                              <td><?= $value['phone'] ?></td>
                              <td><?= $value['datetime'] ?></td>
                              
                              <td class="p-1">
                                <!--<button type="button" class="btn btn-secondary btn-sm p-1" data-bs-toggle="modal" data-bs-target="#modal-edit" onclick="edit(this, 'kasco');"><img src="../img/edit.png" style="width:20px"></button>-->
                                <!--<button type="button" class="btn btn-secondary btn-sm p-1" data-bs-toggle="modal" data-bs-target="#modal-del" onclick="del(this, 'kasco');"><img src="../img/delete.png" style="width:20px"></button>-->
                              </td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
	        
	            </div>
              
              
            </div>
          
        </div>
        
		
	</div>
	
	<!-- Modal-add -->
  <div class="modal fade" id="modal-add" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

      <form id='form_add' action='add.php' method='POST' onsubmit="return false">

        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Добавить код...</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
            <div class="text-danger mb-3"></div>          

          <div class="mb-3">
            <label for="name-input" class="form-label">Код заказа</label>
            <input type="text" class="form-control" name="name" placeholder="" required>
          </div>  
          
          <div class="mb-3">
            <label for="prices" class="form-label">Цена</label>
            <select class="form-select" aria-label="Default select example" name="prices" required>
                <option value="">Установить цену...</option>
                <?php foreach($prices as $price): ?>
                    <option value="<?=$price['id']?>"><?=$price['sum']?></option>
                <?php endforeach; ?>
            </select>
          </div>
         
          <input name="type" placeholder="" hidden="true" value="">

        </div>
        <div class="modal-footer">        
          <button type="submit" class="btn btn-primary">Добавить</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
        </div>

      </form>

      </div>
    </div>
  </div>
	
	
	<!-- Modal-edit -->
  <div class="modal fade" id="modal-edit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

      <form id='form_edit' action='edit.php' method='POST'>

        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Редактировать код...</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
            <div class="text-danger mb-3"></div>          

          <div class="mb-3">
            <label for="name-input" class="form-label">Код заказу</label>
            <input type="text" class="form-control" name="name" placeholder="" required>
          </div>  
          
          <div class="mb-3">
            <label for="prices" class="form-label">Цена</label>
            <select class="form-select" aria-label="Default select example" name="prices" required>
                
                <?php foreach($prices as $price): ?>
                    <option value="<?=$price['id']?>"><?=$price['sum']?></option>
                <?php endforeach; ?>
            </select>
          </div>
          
          <input name="id" placeholder="" hidden="true" value="">
          <input name="type" placeholder="" hidden="true" value="">

        </div>
        <div class="modal-footer">        
          <button type="submit" class="btn btn-primary">Сохранить</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
        </div>

      </form>

      </div>
    </div>
  </div>
  
  
  <!-- Modal-delete -->
  <div class="modal fade" id="modal-del" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

      <form id='form_del' action='del.php' method='POST'>

        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Удалить код...</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
            <div class="text-danger mb-3"></div>
            <div class="form_info mb-3">Операция НЕ обратима! Вы уверены?</div>
          <input name="id" placeholder="" hidden="true" value="">
          <input name="type" placeholder="" hidden="true" value="">

        </div>
        <div class="modal-footer">        
          <button type="submit" class="btn btn-danger">Удалить</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
        </div>

      </form>

      </div>
    </div>
  </div>





<?php else: ?>

    <div class="min-vh-100 d-flex justify-content-center align-items-center">
		<div class="p-4 bg-light rounded shadow" style="min-width:350px">
			<h4>Вход в панель управления:</h4>
			<div class="text-danger mb-2" id="error_info"><?= $_SESSION['error'] ?></div>
			<form method="POST" action="check.php">
				<div class="mb-3">
					<label for="login" class="form-label">Логин</label>
					<input type="text" class="form-control" id="login" name="login">
				</div>
				
				<div class="mb-3">
					<label for="password" class="form-label">Пароль</label>
					<input type="password" class="form-control" id="password" name="password">
				</div>
				
				<button type="submit" class="btn btn-primary">Войти</button>
			</form>
		</div>
	</div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


<script>

    let form_edit = document.querySelector('#form_edit');
    let form_del = document.querySelector('#form_del');
    let form_add = document.querySelector('#form_add');
    let kasco = <?php echo json_encode($codes_kasco); ?>;
    let civilian = <?php echo json_encode($codes_civilian); ?>;
    
    function edit(elem, type){
        if(type == 'kasco'){
            form_edit[2].setAttribute("disabled", "disabled");
        } else {
            form_edit[2].removeAttribute("disabled");
        }
        form_edit[1].value = elem.parentElement.parentElement.children[1].innerText;
        form_edit[2].value = elem.parentElement.parentElement.children[3].innerText;
        form_edit[3].value = elem.parentElement.parentElement.children[0].children[0].innerHTML;
        form_edit[4].value = type;
    }
    
    function del(elem, type){
        form_del[1].value = elem.parentElement.parentElement.children[0].children[0].innerHTML;
        form_del[2].value = type;
    }
    
    function add(type){
        if(type == 'kasco'){
            form_add[2].setAttribute("disabled", "disabled");
        } else {
            form_add[2].removeAttribute("disabled");
        }
        form_add[3].value = type;
    }
    
    form_add.addEventListener('submit', () => {
        if(!kasco){kasco = [];}
        if(!civilian){civilian = [];}
        
        if(kasco.indexOf(form_add[1].value) != -1 || civilian.indexOf(form_add[1].value) != -1){
            form_add.children[1].children[0].innerText = 'Такой код уже существует';
        } else {
            form_add.submit();
        }
        
        
    });
    
</script>

</body>

</html>