<?php

try{

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

    if(isset($_POST['pro_add']) == TRUE){


      /***********************************
      ▼商品名のバリデーション
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
      ▼価格のバリデーション
      ************************************/

      $pro_price = null;

      if(isset($_POST['pro_price']) !== TRUE || mb_strlen($_POST['pro_price']) === 0){

        $error['pro_price'] = '価格を入力してください';

      } else if(mb_strlen($_POST['pro_price']) > 100){

        $error['pro_price'] = '価格は100文字以内で入力してください';

      } else if(preg_match('/^\s*$|^　*$/',$_POST['pro_price'])){ 

      //[fix]数値のみバリデーションさせる

        $error['pro_price'] = '価格は半角、または全角スペースだけでは登録できません';

      } else if (!preg_match('/[1-9]/',$_POST['pro_price'])){

        $error['pro_price'] = '価格は半角数値のみ入力可能です';

      } else {

        $pro_price = $_POST['pro_price'];

      }

      /***********************************
      ▼在庫数のバリデーション
      ************************************/

      $pro_num = null;

      if(isset($_POST['pro_num']) !== TRUE || mb_strlen($_POST['pro_num']) === 0){

        $error['pro_num'] = '在庫数を入力してください';

      } else if(mb_strlen($_POST['pro_num']) > 100){

        $error['pro_num'] = '在庫数は100文字以内で入力してください';

      } else if(preg_match('/^\s*$|^　*$/',$_POST['pro_num'])){ 

      //[must]数値のみバリデーションさせる

        $error['pro_num'] = '在庫数は半角、または全角スペースだけでは登録できません';


      } else if(!preg_match('/[1-9]/',$_POST['pro_num'])){

        $error['pro_num'] = '在庫数は半角数値のみ入力可能です';

      } else {

        $pro_num = $_POST['pro_num'];

      }

      /***********************************
      ▼画像のアップロード
      ************************************/

      if (is_uploaded_file($_FILES['pro_image']['tmp_name']) === TRUE) {

        $pro_image = $_FILES['pro_image']['name'];  

        $extension = pathinfo($pro_image, PATHINFO_EXTENSION); // 拡張子チェック

        if ($extension === 'jpg' || $extension == 'jpeg' || $extension == 'png') {  

          // ユニークID生成し保存ファイルの名前を変更 
          //<MEMO> 画像 : 名前を生成するかは、要件によるが、ランダムに生成するパターンが多い
          //DB : 外部キーは、設定する必要あり 

          $pro_image = md5(uniqid(mt_rand(), true)) . '.' . $extension; 

         // var_dump($pro_image);     
        
            // 同名ファイルが存在するか確認 

            if (is_file($img_dir . $pro_image) !== TRUE) { 

              // ファイルを移動し保存

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
      ▼表示ステータスのバリデーション
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

      if(count($error) === 0){

        $sql = 'INSERT INTO pro_info_table(pro_name,pro_price,pro_image,pro_num,pro_create_date,pro_status) VALUES (?,?,?,?,?,?)';
        $stmt = $dbh->prepare($sql); 
        $data[] = $_POST['pro_name']; //[shold] バリでで変数に入れてるので、、
        $data[] = $_POST['pro_price'];
        $data[] = $pro_image;
        $data[] = $_POST['pro_num'];
        $data[] = date('Y-m-d H:i:s');
        $data[] = $_POST['pro_status'];
        
        
       // $data[] = $_SERVER['REMOTE_ADDR'];
        //$data[] = $_SERVER['REMOTE_HOST'];

        $stmt->execute($data);
        header('Location: http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); // ブラウザをリダイレクトします

      }

    } //  [$_POST] - pro_add

    if(isset($_POST['pro_update']) == TRUE){

    /***********************************

    ▼UPDATE機能 

    ************************************/

      $pro_num = null; 

      if(isset($_POST['pro_num']) !== TRUE || mb_strlen($_POST['pro_num']) === 0){

        $error['pro_num'] = '在庫数を入力してください';

      } else if(mb_strlen($_POST['pro_num']) > 100){

        $error['pro_num'] = '在庫数は100文字以内で入力してください';

      } else if(preg_match('/^\s*$|^　*$/',$_POST['pro_num'])){ 

      //[must]数値のみバリデーションさせる

        $error['pro_num'] = '在庫数は半角、または全角スペースだけでは登録できません';


      } else if(!preg_match('/[1-9]/',$_POST['pro_num'])){

        $error['pro_num'] = '在庫数は半角数値のみ入力可能です';

      } else {

        $pro_num = $_POST['pro_num']; //一つづつ、UPDATEする際は、POSTする変数を共通にすれば良いのではないだろうか。
        $pro_id = $_POST['pro_id'];

      }



      /***********************************

      ▼UPDATEを実行

      ************************************/

      if(count($error) === 0){

        $sql = 'UPDATE pro_info_table SET pro_num = '.$pro_num.' WHERE pro_id = '.$pro_id;
        $stmt = $dbh->prepare($sql);         
        $stmt->execute($data);

        echo "変更しました!";
       // header('Location: http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); // ブラウザをリダイレクトします

      }
    
    } // pro_update

    /***********************************
    ▼ ステータス変更機能
    ************************************/

    $pro_status = null;

    if(isset($_POST['close']) == TRUE){

        $pro_status = 0;
        $pro_id = $_POST['pro_id'];
        $sql = 'UPDATE pro_info_table SET pro_status = '.$pro_status.' WHERE pro_id = '.$pro_id;
        $stmt = $dbh->prepare($sql);         
        $stmt->execute($data);

    } else if(isset($_POST['open']) == TRUE){

        $pro_status = 1;
        $pro_id = $_POST['pro_id'];
        $sql = 'UPDATE pro_info_table SET pro_status = '.$pro_status.' WHERE pro_id = '.$pro_id;
        $stmt = $dbh->prepare($sql);         
        $stmt->execute($data);

    }



  } // [$_POST] 


  /***********************************

  SELECT - 一覧情報取得

  ************************************/

  $sql = 'SELECT pro_id,pro_image,pro_name,pro_price,pro_num,pro_status FROM pro_info_table';
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

  if($rec==false){
    break;
  }

  //$list .= '<input type=hidden name="pro_id" value="'.$rec['pro_id'].'">';

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