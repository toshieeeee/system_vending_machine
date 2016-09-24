<?php

try{

  date_default_timezone_set('Asia/Tokyo');
  $list = '';
  $img_dir = './image/';
  $error = array();
  $data = array();

  /***********************************

  DB Access INFO

  ************************************/

  $dsn = 'mysql:dbname=vending_machine;host=localhost';
  $user = 'root';
  $password = 'root';
  $dbh = new PDO($dsn,$user,$password); //PDO Instance
  $dbh->query('SET NAMES utf8'); // Query run & Access DB

  if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    /***********************************
    ▼投入金額のバリデーション 
    ************************************/

    $pro_price = null;

    if(isset($_POST['pro_price_submit']) !== TRUE || mb_strlen($_POST['pro_price_submit']) === 0){

      $error['pro_price_submit'] = '投入金額を入力してください';

    } else if(mb_strlen($_POST['pro_price_submit']) > 5){

      $error['pro_price_submit'] = '投入金額は5桁以内で入力してください';

    } else if(preg_match('/^\s*$|^　*$/',$_POST['pro_price_submit'])){ 

      $error['pro_price_submit'] = '投入金額は半角、または全角スペースだけでは登録できません';

    } else if (!preg_match('/^[0-9]+$/',$_POST['pro_price_submit'])){

      $error['pro_price_submit'] = '投入金額は正数値のみ入力可能です';

    } else {

      $pro_price_submit = $_POST['pro_price_submit']; //等入金取得
    
    }

    /***********************************
    ▼購入商品のバリデーション
    ************************************/

    $pro_status = null;

    if(isset($_POST['pro_id']) !== TRUE){

       $error['pro_id'] = '商品を選択してください';

    } else {

      $pro_id = $_POST['pro_id']; //商品ID取得

    }

    /***********************************
    ▼SQL実行[SELECT]
    ************************************/

    if(count($error) === 0){ 

       $sql = 'SELECT pro_info_table.pro_id,pro_info_table.pro_image,pro_info_table.pro_name,pro_info_table.pro_price,pro_num_table.pro_num,pro_info_table.pro_status FROM pro_num_table JOIN pro_info_table on pro_info_table.pro_id = pro_num_table.pro_id WHERE pro_info_table.pro_id = '.$pro_id;

      $stmt = $dbh->prepare($sql);

      if($stmt->execute()){

      } else {

        $error['pro_info_table'] = 'SQL失敗:' .$sql;

      }

      /***********************************
      ▼データ取り出し
      ************************************/

      while(true){

        $rec = $stmt->fetch(PDO::FETCH_ASSOC); // Get Result As Associative Array

        if($rec === false){
          break;
        }

        $pro_name = $rec['pro_name'];
        $pro_price = $rec['pro_price'];
        $pro_image = $rec['pro_image'];
        $pro_num = $rec['pro_num'];
        $pro_status = $rec['pro_status'];


        if($pro_price > $pro_price_submit){

          echo '投入金額が足りません';

        } else if($pro_num === '0'){

          echo '在庫切れです';

        } else if($pro_status  === '0'){

            echo '非公開商品です';

        } else {

          $pro_price_result = $pro_price_submit - $pro_price;

          //商品名・画像・お釣りを表示

          $list .= '<p>【'.$pro_name.'】を購入しました！</p>';
          $list .= '<img src="'.$img_dir.$rec['pro_image'].'"</>';
          $list .= '<p>お釣りは'.$pro_price_result.'円です</p>'; //お釣り


          /***********************************
          ▼UPDATEクエリ
          ************************************/

          $pro_update_date = date('Y-m-d H:i:s');
          $pro_num_update = $pro_num - 1;
          $sql = 'UPDATE pro_num_table SET pro_num = '.$pro_num_update.',pro_update_date = "'.$pro_update_date.'" WHERE pro_id = '.$pro_id;
          $stmt = $dbh->prepare($sql); 

          if($stmt->execute($data)){

          }else{

            $error['pro_num_update'] = 'SQL失敗:' .$sql;  

          }

        }


      //Disconnect DB

      $dbh = null;

      } //WHILE

    } // COUNT

  } //  $_POST

}catch(Exception $e){

  echo 'ただいま障害により大変ご迷惑をおかけしております';
  exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <style type="text/css">
    
    img {
    width: 480px;
  }

  </style>
</head>
<body>

  <?php if(count($error) > 0){ ?>

    <ul>

      <?php foreach ($error as $error_text) { ?>

      <li><?php echo $error_text ?></li>

      <?php } ?>

    </ul>

  <?php } ?>

  <div>
      <?php echo $list ?>
  </div>

  <footer><a href="index.php">戻る</a></footer>
  
</body>
</html>
