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

  ▼INSERT機能 

  ************************************/

    if(isset($_POST['pro_add']) === TRUE){


      /***********************************
      ▼商品名のバリデーション [INSERT]
      ************************************/

      $pro_name = null;

      if(isset($_POST['pro_name']) !== TRUE || mb_strlen($_POST['pro_name']) === 0){

        $error['pro_name'] = '商品名を入力してください';

      } else if(mb_strlen($_POST['pro_name']) > 20){

        $error['pro_name'] = '商品名は20文字以内で入力してください';

      } else if(preg_match ('/^\s*$|^　*$/',$_POST['pro_name'])){

        $error['pro_name'] = '商品名は半角、または全角スペースだけでは登録できません';

      } else {

        $pro_name = $_POST['pro_name'];    

      }

      /***********************************
      ▼価格のバリデーション [INSERT]
      ************************************/

      $pro_price = null;

      if(isset($_POST['pro_price']) !== TRUE || mb_strlen($_POST['pro_price']) === 0){

        $error['pro_price'] = '価格を入力してください';

      } else if(mb_strlen($_POST['pro_price']) > 100){

        $error['pro_price'] = '価格は100文字以内で入力してください';

      } else if(preg_match('/^\s*$|^　*$/',$_POST['pro_price'])){ 

        $error['pro_price'] = '価格は半角、または全角スペースだけでは登録できません';

      } else if (!preg_match('/^[1-9]+$/',$_POST['pro_price'])){

        $error['pro_price'] = '価格は正数値のみ入力可能です';

        //整数のみバリデーションさせる。(is_numeric関数で判断 → 型を見たらどうしても、文字列に変換されてしまうみたいなので、だめ？)

      } else {

        $pro_price = $_POST['pro_price'];

      }

      /***********************************
      ▼在庫数のバリデーション [INSERT]
      ************************************/

      $pro_num = null;

      if(isset($_POST['pro_num']) !== TRUE || mb_strlen($_POST['pro_num']) === 0){

        $error['pro_num'] = '在庫数を入力してください';

      } else if(mb_strlen($_POST['pro_num']) > 100){

        $error['pro_num'] = '在庫数は100文字以内で入力してください';

      } else if(preg_match('/^\s*$|^　*$/',$_POST['pro_num'])){ 

      //[must]数値のみバリデーションさせる

        $error['pro_num'] = '在庫数は半角、または全角スペースだけでは登録できません';


      } else if(!preg_match('/^[1-9]+$/',$_POST['pro_num'])){

        $error['pro_num'] = '在庫数は半角正数値のみ入力可能です';

      } else {

        $pro_num = $_POST['pro_num'];

      }

      /***********************************
      ▼画像のアップロード [INSERT]
      ************************************/

      if (is_uploaded_file($_FILES['pro_image']['tmp_name']) === TRUE) {

        $pro_image = $_FILES['pro_image']['name'];  

        $extension = pathinfo($pro_image, PATHINFO_EXTENSION); // 拡張子チェック

        if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'JPG' || $extension === 'png') {  

          // ユニークID生成し保存ファイルの名前を変更 
          //<MEMO> 画像 : 名前を生成するかは、要件によるが、ランダムに生成するパターンが多い
          //DB : 外部キーは、設定する必要あり 

          $pro_image = md5(uniqid(mt_rand(), true)) . '.' . $extension; 
        
            if (is_file($img_dir . $pro_image) !== TRUE) { 

              if (move_uploaded_file($_FILES['pro_image']['tmp_name'], $img_dir . $pro_image) !== TRUE) {

                  $error[] = 'ファイルアップロードに失敗しました';

              } 
              
            } else { // 生成したIDがかぶることは通常ないため、IDの再生成ではなく再アップロードを促すようにした 

              $error[] = 'ファイルアップロードに失敗しました。再度お試しください。';
            }

          } else { 

            $error[] = 'ファイル形式が異なります。画像ファイルはJPEG又はPNGのみ利用可能です。';

          }     

      } else {

        $error[] = 'ファイルを選択してください';

      }

      /***********************************
      ▼表示ステータスのバリデーション [INSERT]
      ************************************/

      $pro_status = null;

      if(isset($_POST['pro_status']) !== TRUE){

         $error['pro_status'] = '表示ステータスを選択してください';

      } else {

        $pro_status = $_POST['pro_status'];

      }

      /***********************************

      ▼INSERTを実行

      ************************************/


      if(count($error) === 0) {

      /************************************
      トランザクションの開始 [INSERT]
      *************************************/
      
        $dbh->beginTransaction(); 

        $sql_info = 'INSERT INTO pro_info_table(pro_name,pro_price,pro_image,pro_create_date,pro_status) VALUES (?,?,?,?,?)';
        $stmt = $dbh->prepare($sql_info); 

        $data[] = $_POST['pro_name']; //[shold] バリでで変数に入れてるので、、
        $data[] = $_POST['pro_price'];
        $data[] = $pro_image;
        $data[] = date('Y-m-d H:i:s');
        $data[] = $_POST['pro_status'];

        if($stmt->execute($data)){ // SQLの判定 / 実行

        } else {

          $error['pro_info_table'] = 'SQL失敗:' .$sql_info;

        }
    
        $sql_num = 'INSERT INTO pro_num_table(pro_num,pro_create_date) VALUES (?,?)';

        $stmt = $dbh->prepare($sql_num); 
        $num[] = $_POST['pro_num'];
        $num[] = date('Y-m-d H:i:s');

        if($stmt->execute($num)){

        } else {

          $error['pro_num_table'] = 'SQL失敗:' .$sql_num;
        }

      /************************************
      トランザクションの成否判定 [INSERT]
      *************************************/
      
      if(count($error) === 0) {

        $dbh->commit(); // コミット
        header('Location: http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); // ブラウザをリダイレクト

      } else {

      $dbh->rollback(); //ロールバック

      }

      

      }

    } //  $_POST['pro_add']



    /***********************************

    ▼UPDATE機能 

    ************************************/

    /***********************************
    ▼「在庫数変更」機能 
    ************************************/

    /***********************************
    ▼在庫数のバリデーション [UPDATE]
    ************************************/

    if(isset($_POST['pro_update']) === TRUE){

      $pro_num = null; 

      if(isset($_POST['pro_num']) !== TRUE || mb_strlen($_POST['pro_num']) === 0){

        $error['pro_num'] = '在庫数を入力してください';

      } else if(mb_strlen($_POST['pro_num']) > 100){

        $error['pro_num'] = '在庫数は100文字以内で入力してください';

      } else if(preg_match('/^\s*$|^　*$/',$_POST['pro_num'])){ 

      //[must]数値のみバリデーションさせる

        $error['pro_num'] = '在庫数は半角、または全角スペースだけでは登録できません';


      } else if(!preg_match('/^[1-9]+$/',$_POST['pro_num'])){

        $error['pro_num'] = '在庫数は半角正数値のみ入力可能です';

      } else {

        $pro_id = $_POST['pro_id'];
        $pro_num = $_POST['pro_num']; 
        
      }


      /***********************************
      ▼在庫数クエリ実行 [UPDATE]
      ************************************/

      if(count($error) === 0){

        $pro_update_date = date('Y-m-d H:i:s');
        $sql = 'UPDATE pro_num_table SET pro_num = '.$pro_num.',pro_update_date = "'.$pro_update_date.'" WHERE pro_id = '.$pro_id;
        $stmt = $dbh->prepare($sql); 

        if($stmt->execute($data)){ // クエリ判定/実行

          //echo "「在庫数」変更しました!";
          
          header('Location: http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); // ブラウザをリダイレクト

        } else {

            $error['pro_num_update'] = 'SQL失敗:' .$sql;            

        }

      }
    
    } // $_POST['pro_update'];

    /***********************************
    ▼ ステータス変更機能 [update]
    ************************************/

    $pro_status = null;

    if(isset($_POST['close']) === TRUE || isset($_POST['open']) === TRUE){

      $pro_id = $_POST['pro_id'];
      $pro_update_date = date('Y-m-d H:i:s');

      if(isset($_POST['close']) === TRUE){

         $pro_status = 0;
                
      } else if(isset($_POST['open']) === TRUE){

          $pro_status = 1;
      }

      $sql = 'UPDATE pro_info_table SET pro_status = '.$pro_status.' , pro_update_date = "'.$pro_update_date.'" WHERE pro_id = '.$pro_id;
      $stmt = $dbh->prepare($sql);         
      
      if($stmt->execute($data)){

        //echo "ステータス変更しました!";
        header('Location: http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); // ブラウザをリダイレクト

      } else {

        $error['pro_update'] = 'SQL失敗:' .$sql;            

      }

    } // [$POST[open] || [close]]

  } /********** [$_POST] *************/


  /***********************************

  SELECT - 一覧情報取得 - テーブルを結合してクエリを実行する。

  ************************************/

  $sql = 'SELECT pro_info_table.pro_id,pro_info_table.pro_image,pro_info_table.pro_name,pro_info_table.pro_price,pro_num_table.pro_num,pro_info_table.pro_status FROM pro_num_table JOIN pro_info_table on pro_info_table.pro_id = pro_num_table.pro_id';

  $stmt = $dbh->prepare($sql);
  $stmt->execute(); 

  //Disconnect DB

  $dbh = null;
  
}catch(Exception $e){

  echo 'ただいま障害により大変ご迷惑をおかけしております';
  exit();
}

while(true){

  $rec = $stmt->fetch(PDO::FETCH_ASSOC); // Get Result As Associative Array

  if($rec === false){
    break;
  }


  if($rec['pro_status'] === '1'){

    $list .= '<tr class="open">'; // 

  } else{

    $list .= '<tr class="close">';

  }

  $list .= '<td>' .$rec['pro_name'].'</td>';
  $list .= '<td>' .$rec['pro_price'].'</td>';
  $list .= '<td><img src="'.$img_dir.$rec['pro_image'].'"</></td>';

  $list .= '<form method="post" action="tool.php">
              <input type=hidden name="pro_id" value="'.$rec['pro_id'].'">
              <td class="pro_num">
                <input type="text" name="pro_num"  value=' .$rec['pro_num'].'>
                <input type="submit" name="pro_update" value="update">
              </td>
            </form>';

  $list .= '<form method="post" action="tool.php">

              <input type=hidden name="pro_id" value="'.$rec['pro_id'].'">';

            if($rec['pro_status'] === '1'){

              $list .= '<td>
                          <input type="submit" name ="close" value="公開 → 非公開">
                        </td>';

            }else {

              $list .= '<td>
                          <input type="submit" name ="open" value="非公開 → 公開">
                        </td>';
            }

  $list .= '</form>';
  $list .= '</tr>';

}

/******************************************************
PHP Code END 
*******************************************************/

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CodeCamp</title>
  
  <style type="text/css">
    
  table,td,th {
    border: solid black 1px;
    margin : auto;
  }

  td,th {
    min-width: 120px;

    text-align: left;
    padding-left: 8px;

  }

  table {
      width: 350px;
      margin-top: 10px;
  }

  img {
    width: 480px;
  }

  .open {
    background: #fff;
  }

  .close {
    background: #ccc;
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


  <form method="post" action="tool.php" enctype="multipart/form-data">

    <p>▼商品名を入力してください</p>

    <input type="text" name="pro_name">

    <p>▼価格を入力してください</p>

    <input type="text" name="pro_price"> 円

    <p>▼個数を入力してください</p>

    <input type="text" name="pro_num"> 個

    <p>▼画像を選択してください</p>

    <input type="file" name="pro_image">

    <p>▼表示ステータスを選択してください</p>

    <input type="radio" name="pro_status" value="1"> 表示
    <input type="radio" name="pro_status" value="0"> 非表示

    <div>

      <!--<input type="button" onclick="history.back()" value="戻る">-->
      <br>
      <input type="submit" name="pro_add" value="OK">

    </div>

  </form>

  <br><hr><br>

  <table>

    <p>▼商品一覧</p>

    <tbody>

      <tr>
          <th>商品名</th>
          <th>価格</th>
          <th>商品画像</th>
          <th>在庫数</th>
          <th>ステータス</th>
      </tr>

      <!--ここにPHPのコードを書きます-->

        <?php echo $list ?>

    </tbody>

  </table>


<!--
[フロント側の実装は、後回しにしましょう]

<script type="text/javascript" src="./js/jquery-2.1.0.min.js"></script>
<script type="text/javascript">
 
if($('.pro_status').text() == 1){

  console.log("1");

}else{

  console.log("0");

};


</script>

-->

</body>

</html>