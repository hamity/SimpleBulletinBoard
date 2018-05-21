<?php
//SQLに使用する用語の定義
define("DB_HOST", "localhost");
define("DB_NAME", "bbs_php");
define("DB_CHARSET", "utf8");
define("DB_USER", "root");
define("DB_PASSWORD", "root");
define("DB_TABLE", "posts");

try{
    //DBへの接続
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));        //DB接続できた物に対する、操作を行う.今回はエラーレポートに対し、例外を投げるように設定している(これにより try, catchが使用可能)

    $db_post = $db->query("select * from posts");

    $db = null;
}
catch(PODException $e){
    echo "Error: read mysql";
    exit('データベース接続失敗'.$e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>簡易掲示板</title>
</head>
<body>
    <h1>簡易掲示板</h1>
    <form action="./input.php" method="post">
        message: <input type="text" name="message" contents="no-cache">
        user: <input type="text" name="user" contents="no-cache">
        <input type="hidden" name="url" value=<?php print((empty($_SERVER["HTTPS"]) ? "http://" : "https://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);?>>
        <input type="submit" value="投稿">
    </form>
    <h2>投稿件数(
        <?php
        $postsNum = $db_post->rowCount();
        echo (int)$postsNum;
        ?>件)</h2>
    <ul>
        <?php
        if($postsNum > 0){
            foreach($db_post as $post){
                echo ShowPostData($post, (int)$post["rarity"]);
            }
        }
        else if($postsNum === 0){
            echo "まだ投稿はありません";
        }
        else{
            echo "Error: $postNumに不正な値が入力されています";
        }
        ?>
    </ul>
</body>
</html>

<?php
    function ShowPostData($post, $rarity){
        $text = htmlspecialchars($post['text'], ENT_QUOTES, 'UTF-8');
        $username = htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8');
        $date = htmlspecialchars($post['date']);
        if($rarity === 1){
            return '<li><h1 style=" color:red ">'.$text.' ('.$username.') - '.$date.'</h1>';
        }else if($rarity === 2){
            return '<li><h2>'.$text.' ('.$username.') - '.$date.'</h2>';
        }else if($rarity === 3){
            return '<li><h3>'.$text.' ('.$username.') - '.$date.'</h3>';
        }else if($rarity === 4){
            return '<li>'.$text.' ('.$username.') - '.$date;
        }else{
            return "Error: Do'nt exist number of $rarity";
        }
    }
?>
