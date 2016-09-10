<?php

/*

▼specifications 

File Name : challenge_mysql_select.php
choose All → Show All records "emp_table"
choose manager → show Only record "manager"

*/

try{


    //Define val 

  $list = '';

    /***********************************
    DB Access INFO
    ************************************/

    $dsn = 'mysql:dbname=vending_machine;host=localhost';
    $user = 'root';
    $password = 'root';
    $dbh = new PDO($dsn,$user,$password); //PDO Instance
    $dbh->query('SET NAMES utf8'); // Query run & Access DB

    /***********************************
    SELECT 
    ************************************/


    $sql = 'SELECT pro_name,pro_price,pro_status FROM pro_info_table';


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

  $list .= '<tr>';
  $list .= '<td>' .$rec['pro_name'].'</td>';
  $list .= '<td>' .$rec['pro_price'].'</td>';
  $list .= '<td>' .$rec['pro_status'].'</td>';
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

  }

  td,th {
    
    text-align: left;
    padding-left: 8px;

  }

  table {
      width: 350px;
      margin-top: 10px;
  }

  </style>

</head>

<body>

    <table>

    <p>商品一覧</p>

    <tbody>

      <tr>
         <!-- <th>商品画像</th>-->
          <th>商品名</th>
          <th>価格</th>
          <!--<th>在庫数</th>-->
          <th>ステータス</th>
      </tr>

      <tr>

      <!--ここにPHPのコードを書きます-->

        <?php echo $list ?>

      </tr>

    </tbody>

    </table>

</html>