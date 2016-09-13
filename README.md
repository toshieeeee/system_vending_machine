#仕様 - tool.php

##▼機能

    1.DBに商品を追加する機能(INSERT)

    2.DBの商品を変更する機能(UPDATE)

    3.DBの商品を、一覧表示する機能(SELECT)


###1.DBに商品を追加する機能（INSERT）

* 追加するレコード

    * 商品名 
    * 値段 
    * 在庫数
    * 画像
    * 公開ステータス
    * _追加日_


###2.DBの商品を変更する機能(UPDATE)


＊ 変更するレコード

    * 在庫数
    * 公開ステータス
    * _変更日_

###3.DBの商品を、一覧表示する機能(SELECT)

* 表示するレコード

    * 商品名 
    * 値段
    * 在庫数
    * 画像
    * 公開ステータス


##▼作業フロー

  ▼MySQL

    * テーブルの設計

    * テーブルの作成

    * テーブルの正規化 / 外部キー設定

  ▼PHP

    * INSERT 

      HTTP-POSTで実行 → 入力データ受け取り → バリデーション → INSERTクエリ実行 → DBに登録

    * UPDATE

      HTTP-POSTで実行 → 入力データ受け取り → バリデーション → UPDATEクエリ実行 → DBに登録

    * SELECT

      HTTP-GETで実行 → SELECTクエリ実行 → バリデーション → HTMLに出力


##▼クエリ


* INSERT クエリ  => pro_info_table

    INSERT INTO pro_info_table (pro_name,pro_price,pro_create_date,pro_update_date,pro_status) VALUES('TEA','130',now(),now(),0)


* UPDATE クエリ  => pro_info_table

    UPDATE pro_info_table SET pro_image='c076f265d5f4899f22f04d5a5120a5b6.jpg' WHERE pro_id=1;


##▼コメント


    * テーブル結合して、クエリを実行
    * 画像情報をテーブルに追加して、表示


##合格ライン仕様 BY CODECAMP


###▼機能

    *「ドリンク名」「値段」「在庫数」「公開ステータス」を入力し、商品を追加できる。

    * 商品を追加する場合、「商品画像」を指定してアップロードできる。

    * 追加した商品の一覧情報として、「商品画像」、「商品名」、「値段」、「在庫数」、「公開ステータス」のデータを一覧で表示する。

    * 商品一覧から指定ドリンクの在庫数を入力し、在庫数の変更ができる。

    * 商品一覧から指定ドリンクの公開ステータス「公開」あるいは「非公開」の変更ができる。

    * 商品の追加あるいは指定ドリンク情報（「在庫数」、「公開ステータス」）の変更が正常に完了した場合、完了のメッセージを表示する。

    ###▼バリデーション条件  


    * 商品を追加する場合、「商品名」「値段」、「在庫数」、「公開ステータス」「商品画像」のいずれかを指定していない場合、エラーメッセージを表示して、商品を追加できない。

    * 商品を追加する場合、「値段」、「在庫数」は正の整数のみ可能とする。正の整数以外はエラーメッセージを表示して、商品を追加できない

    * 商品を追加する場合、公開ステータスは「公開」あるいは「非公開」のみ可能とする。「公開」あるいは「非公開」以外はエラーメッセージを表示して、商品を追加できない。

    * アップロードできる「商品画像」のファイル形式は「JPEG」、「PNG」のみ可能とする。「JPEG」、「PNG」以外はエラーメッセージを表示して、商品を追加できない。

    * 商品一覧から指定ドリンクの在庫数を変更する場合、正の整数のみ可能とする。正の整数以外はエラーメッセージを表示して、変更できない。

