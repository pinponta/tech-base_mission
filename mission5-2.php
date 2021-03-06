<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
    	// DB接続設定
    	$dsn = 'データベース名';
    	$user = 'ユーザー名';
    	$password = 'パスワード名';
    	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    	
        //テーブルの設定
        $sql = "CREATE TABLE IF NOT EXISTS tbtest"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date TIMESTAMP"
        .");";
        $stmt = $pdo->query($sql);
        
        //毎回tbpassは削除
    	$sql = 'DROP TABLE tbpass';
		$stmt = $pdo->query($sql);
		//パスワードを格納する２つ目のテーブルの設定
    	$sql = "CREATE TABLE IF NOT EXISTS tbpass"
    	." ("
    	. "passcord TEXT"
    	.");";
    	$stmt = $pdo->query($sql);
    	$sql = $pdo -> prepare("INSERT INTO tbpass (passcord) VALUES (:passcord)");
    	$sql -> bindParam(':passcord', $passcord, PDO::PARAM_STR);
    	if($passcord == NULL)
    	{
    	$passcord = 'intern'; //２つ目のテーブルのパスワードをinternに設定
    	$sql -> execute();
    	}


    	$sql = 'SELECT * FROM tbpass';
    	$stmt = $pdo->query($sql);
    	$results = $stmt->fetchAll();
    	foreach ($results as $row){
    		$passcord = $row['passcord'];//パスワードは$passcordに入れる
    	}
    	
    	//削除するプログラム
        if(!empty($_POST["dnumber"])&&!empty($_POST["password2"]))
        {
            $pass2 = $_POST["password2"];
            if($pass2 == $passcord)
            {
               	$id = $_POST["dnumber"];//削除する投稿番号
            	$sql = 'delete from tbtest where id=:id';
            	$stmt = $pdo->prepare($sql);
            	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
            	$stmt->execute(); 
            }
            else
            {
                echo "パスワードが違います<br>";                
            }
        }
        elseif(empty($_POST["dnumber"])&&!empty($_POST["password2"]))
        {
            echo "削除する番号が入力されていません<br>";
        }
        elseif(!empty($_POST["dnumber"])&&empty($_POST["password2"]))
        {
            echo "パスワードが入力されていません<br>";
        }
        
        //編集する部分を抽出するプログラム
        if(!empty($_POST["enumber"])&&!empty($_POST["password3"]))
        {
            $enumber=$_POST["enumber"];
            $pass3 = $_POST["password3"];
            if($pass3 == $passcord)
            {
                $id = $enumber ; // idがこの値のデータだけを抽出したい、とする
                $sql = 'SELECT * FROM tbtest WHERE id=:id ';
                $stmt = $pdo->prepare($sql);                  // 差し替えるパラメータを含めて記述したSQLを準備し、
                $stmt->bindParam(':id', $id, PDO::PARAM_INT); // その差し替えるパラメータの値を指定してから、
                $stmt->execute();                             // SQLを実行する。
                $results = $stmt->fetchAll(); 
                foreach ($results as $row)
                {
            		//$rowの中にはテーブルのカラム名が入る
            		$n = $row['name'];
            		$c = $row['comment'];
                }
            }
            else
            {
                echo "パスワードが違います<br>";                                
            }
        }
        elseif(empty($_POST["enumber"])&&!empty($_POST["password3"]))
        {
            echo "編集する番号が入力されていません<br>";
        }
        elseif(!empty($_POST["enumber"])&&empty($_POST["password3"]))
        {
            echo "パスワードが入力されていません<br>";
        }
          
        //編集する部分
        if(!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["sign"])&&!empty($_POST["password1"]))
        {
            $enumber=$_POST["sign"];
            $pass1 = $_POST["password1"];
            if($pass1 == $passcord)
            {
                $id = $enumber; //変更する投稿番号
                $name = $_POST["name"];
                $comment = $_POST["comment"]; //変更したい名前、変更したいコメントは自分で決めること
                $date = date("Y/m/d H:i:s");
                $sql = 'UPDATE tbtest SET name=:name,comment=:comment,date=:date WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
            else
            {
                echo "パスワードが違います<br>";     
            }
        }
        elseif(empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["sign"])&&!empty($_POST["password1"]))
        {
            echo "名前が入力されていません<br>";
        }
        elseif(!empty($_POST["name"])&&empty($_POST["comment"])&&!empty($_POST["sign"])&&!empty($_POST["password1"]))
        {
            echo "コメントが入力されていません<br>";
        }
        elseif(!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["sign"])&&empty($_POST["password1"]))
        {
            echo "パスワードが入力されていません<br>";
        }

        //新規入力する部分
        if(!empty($_POST["name"])&&!empty($_POST["comment"])&&empty($_POST["sign"])&&!empty($_POST["password1"]))
        {
            $pass1 = $_POST["password1"];
            if($pass1 == $passcord)
            {
            	$sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, date) VALUES (:name, :comment, :date)");
            	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
            	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
            	$name = $_POST["name"];
            	$comment = $_POST["comment"]; //好きな名前、好きな言葉は自分で決めること
                $date = date("Y/m/d H:i:s");
            	$sql -> execute();
            	//bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。最適なものを適宜決めよう。
            }
            else
            {
                echo "パスワードが違います<br>";     
            }
        }
        elseif(empty($_POST["name"])&&!empty($_POST["comment"])&&empty($_POST["sign"])&&!empty($_POST["password1"]))
        {
            echo "名前が入力されていません<br>";
        }
        elseif(!empty($_POST["name"])&&empty($_POST["comment"])&&empty($_POST["sign"])&&!empty($_POST["password1"]))
        {
            echo "コメントが入力されていません<br>";
        }
        elseif(!empty($_POST["name"])&&!empty($_POST["comment"])&&empty($_POST["sign"])&&empty($_POST["password1"]))
        {
            echo "パスワードが入力されていません<br>";
        }
    ?>
    
        【投稿フォーム】
    <form action="" method="post">
        名前：　　　<input type="text" name="name" placeholder="名前" value="<?php if(!empty($_POST["enumber"])){echo $n;} ?>"><br>
        コメント：　<input type="text" name="comment" placeholder="コメント" value="<?php if(!empty($_POST["enumber"])){echo $c;} ?>"><br>
        パスワード：<input type="text" name="password1" placeholder="パスワード">
        <input hidden="text" name="sign" value="<?php if(!empty($_POST["enumber"])){echo $_POST["enumber"];} ?>">
        <input type="submit" name="submit">
    </form>
    <br>
    <form action="" method="post"> 
        削除番号：　<input type="text" name="dnumber" placeholder="削除対象番号"><br>
        パスワード：<input type="text" name="password2" placeholder="パスワード">
        <input type="submit" name="delete" value="削除">
    </form>
    <br>
    <form action="" method="post">
        編集番号：　<input type="text" name="enumber" placeholder="編集対象番号"><br>
        パスワード：<input type="text" name="password3" placeholder="パスワード">
        <input type="submit" name="edit" value="編集">
    </form>

    <?php
            echo "【投稿一覧】<br>";
            $sql = 'SELECT * FROM tbtest';//表示
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row)
                {
                //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
                }      
    ?>
</body>
</html>