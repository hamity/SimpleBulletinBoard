<?php
//SQLに使用する用語の定義
define("DB_HOST", "localhost");
define("DB_NAME", "bbs_php");
define("DB_CHARSET", "utf8");
define("DB_USER", "root");
define("DB_PASSWORD", "root");
define("DB_TABLE", "posts");

//rarityに使用する数値
define("SS_RARE", 4);
define("S_RARE", 16);
define("RARE", 41);

//使用するデフォルトのタイムゾーンを設定
date_default_timezone_set('UTC');
$date_time = new DateTime();

//タイムゾーンを東京に設定
$date_time->setTimeZone(new DateTimeZone('Asia/Tokyo'));
if(!isset($date_time)){
    echo "datetime is Error!!!";
    exit;
}

try{
    //DBへの接続
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));        //DB接続できた物に対する、操作を行う.今回はエラーレポートに対し、例外を投げるように設定している(これにより try, catchが使用可能)

    //postデータを受け取った際の処理
    $id = $_POST['id'];
    $user_name = trim($_POST['user']);
    $message = trim($_POST['message']);

    $db_post = $db->query("select * from posts");

    if(isset($_POST) && !empty($user_name) && !empty($message)){
         //DB接続
        $sql_text = $db->prepare('insert into '.DB_TABLE.' (date, username, text, rarity) values (?, ?, ?, ?)');

        //レア度の決定
        $rare_num = rand(1, 100);
        $rare = random_rarity($rare_num);

        //用意したmysql用の変数に具体的な値を代入する
        $sql_text->bindValue(1, $date_time->format('Y-m-d H:i:s'));
        $sql_text->bindValue(2, $user_name);
        $sql_text->bindValue(3, $message);
        $sql_text->bindValue(4, $rare, PDO::PARAM_INT);
        //実行をセット
        $sql_text->execute();

        // $sql_text->execute(
        //     array(
        //         ':date'=>$date_time->format('Y-m-d H:i:s'),
        //         ':username'=>$user_name,
        //         ':text'=>$message,
        //         ':rarity'=>$rare,
        //     )
        // );
    }
    $db = null;
}
catch(PODException $e){
    echo "Error: read mysql";
    exit('データベース接続失敗'.$e->getMessage());
}

header('Location: '.$_POST["url"]);


//レア度を決めるメソッド
function random_rarity($rare_num){
    if($rare_num < SS_RARE){
        return 1;
    }else if($rare_num < S_RARE){
        return 2;
    }else if($rare_num < RARE){
        return 3;
    }else{
        return 4;
    }
}

?>