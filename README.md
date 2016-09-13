# system_vending_machine


##仕様 - tool.php

###機能

１、DBに商品を追加する機能
２、DBの商品を、一覧表示する機能


####１、DBに商品を追加する機能（INSERT）


＊追加するレコード

1.商品名
2.値段
3.在庫数
4.画像
5.公開ステータス


####２、DBの商品を、一覧表示する機能（ SELECT）


＊取得するレコード

1.商品画像
2.商品名
3.価格
4.在庫数
5.ステータス

＊テーブル詳細

####. 在庫数

・変更ボタンを押す → DBに変更が反映（UPDATE）

####.ステータス

・「公開」「非公開」の2種類
・「非公開」ステータスに変更すると、背景が灰色になる。



##▼作業フロー tool.php 


1.テーブル作成（正規化/外部キーの設定）

・商品情報（ドリンク情報）- pro_info_table
・在庫管理テーブル - pro_num_table
・購入履歴テーブル - pro_history_table

＊商品情報

・商品ID
・商品名
・値段
・在庫数


2.SELECTクエリを、実行するPHPを実装（情報表示機能）

3.INSERTクエリを実行するPHPを実装（商品追加機能）


##▼クエリ


＊INSERT クエリ  => pro_info_table

INSERT INTO pro_info_table (pro_name,pro_price,pro_create_date,pro_update_date,pro_status) VALUES('TEA','130',now(),now(),0)

##▼コメント

・テーブル結合して、クエリを実行
・画像情報をテーブルに追加して、表示
