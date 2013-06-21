<?php
/* 初期設定 */
//ini_set('display_errors', 'On');

$readme = true;include 'readme.php';

if ($_REQUEST['c']) {
    session_start();
    session_destroy();
    session_start();
    unset($_SESSION['rtoken'], $_SESSION['rtoken_s']);
}

session_start();
include('HTTP/OAuth/Consumer.php');
$ver = 'v4.1.3';


/* ─────────────────────────── */
/* マニュアル設定 */
/* ─────────────────────────── */

/* !!!!! 下記はサンプルです。自分の設定に変更して下さい !!!!! */

// コンシューマーキー&シークレット
$consumer_key = 'コンシューマーキー';
$consumer_key_secret = 'シークレット';
// コールバックURL
$callback_url = 'http://dev.git6.com/twitter/louiseuwaaan/';

// リツイートするツイートID(任意)
$retweet_id = '';

// つぶやくメッセージ
$message[] = 'ルイズ！ルイズ！ルイズ！ルイズぅぅうううわぁああああああああああああああああああああああん！！！
あぁああああ…ああ…あっあっー！あぁああああああ！！！ルイズルイズルイズぅううぁわぁああああ！！！
あぁクンカクンカ！クンカクンカ！スーハースーハー！スーハースーハー！';

$message[] = 'いい匂いだなぁ…くんくん
んはぁっ！ルイズ・フランソワーズたんの桃色ブロンドの髪をクンカクンカしたいお！クンカクンカ！あぁあ！！
間違えた！モフモフしたいお！モフモフ！モフモフ！髪髪モフモフ！カリカリモフモフ…きゅんきゅんきゅい！！
小説11巻のルイズたんかわいかったよぅ！！';

$message[] = 'あぁぁああ…あああ…あっあぁああああ！！ふぁぁあああんんっ！！
アニメ2期決まって良かったねルイズたん！あぁあああああ！かわいい！ルイズたん！かわいい！あっああぁああ！
コミック2巻も発売されて嬉し…いやぁああああああ！！！にゃああああああああん！！ぎゃああああああああ！！';

$message[] = 'ぐあああああああああああ！！！コミックなんて現実じゃない！！！！あ…小説もアニメもよく考えたら…
ル イ ズ ち ゃ ん は 現実 じ ゃ な い？にゃあああああああああああああん！！うぁああああああああああ！！
そんなぁああああああ！！いやぁぁぁあああああああああ！！';

$message[] = 'はぁああああああん！！ハルケギニアぁああああ！！
この！ちきしょー！やめてやる！！現実なんかやめ…て…え！？見…てる？表紙絵のルイズちゃんが僕を見てる？
表紙絵のルイズちゃんが僕を見てるぞ！ルイズちゃんが僕を見てるぞ！挿絵のルイズちゃんが僕を見てるぞ！！';

$message[] = 'アニメのルイズちゃんが僕に話しかけてるぞ！！！
よかった…世の中まだまだ捨てたモンじゃないんだねっ！
いやっほぉおおおおおおお！！！僕にはルイズちゃんがいる！！やったよケティ！！ひとりでできるもん！！！';

$message[] = 'あ、コミックのルイズちゃああああああああああああああん！！いやぁあああああああああああああああ！！！！
あっあんああっああんあアン様ぁあ！！セ、セイバー！！シャナぁああああああ！！！ヴィルヘルミナぁあああ！！ううっうぅうう！！俺の想いよルイズへ届け！！ハルケギニアのルイズへ届け！';

$message[] = 'このルイズうわあああんは自動でポストされました！
あなたもルイズうわああんしませんか？ルイズをクリックするだけの簡単なお仕事です！URL : http://dev.git6.com/twitter/louiseuwaaan/  #louiseuwaaan';

// データベース情報
//データベース記録を有効にする場合はtrue
$database['use'] = true;
//上記設定をtrueにした場合DB接続情報を入れて下さい。
$database['host'] = "localhost";
$database['user'] = "user";
$database['passwd'] = "passwd";
$database['db'] = "twitter";


/* ─────────────────────────── */
/* DB Functions */
if ($database['use'] === true) {
    $connect = new mysqli($database['host'], $database['user'], $database['passwd'], $database['db']) or die("");
    $stmt = $connect->prepare("SET NAMES utf8");
    $result = $stmt->execute();
    // データベース接続判定
    if ($result) {
        $database['use'] = true;
    } else {//接続出来ない場合は強制的に不使用
        $database['use'] = false;
    }
}

function getCount($connect, $uniq = false) {
    /* Return BIGINT */
    if ($uniq) {
        $query = "SELECT COUNT(DISTINCT twitter_id) FROM `louise`";
    } else {
        $query = "SELECT COUNT(no) FROM `louise`";
    }
    if (($result = $connect->query($query)) === false) {
        $return = false;
    } else {
        $array = $result->fetch_assoc();
        if (isset($array['COUNT(no)'])) {
            $return = $array['COUNT(no)'];
        } else {
            $return = $array['COUNT(DISTINCT twitter_id)'];
        }
    }
    return $return;
}

function postCount($connect, $twitter_id, $token, $token_s, $ip, $host, $agent) {
    $query = "INSERT INTO `louise` (`no`, `twitter_id`, `token`, `token_sec`, `ip`, `host`, `agent`, `regist_time`) VALUES ('', '$twitter_id', '$token', '$token_s', '$ip', '$host', '$agent', NOW())";
    if (($result = $connect->query($query)) === false) {
        $return = false;
    } else {
        $return = true;
    }
    return $return;
}

//var_dump($_SESSION);
/* ─────────────────────────── */
/* 処理開始 main */
$consumer = new HTTP_OAuth_Consumer($consumer_key, $consumer_key_secret);
if (!isset($_SESSION['rtoken']) or $_SESSION['rtoken'] == '' or !isset($_SESSION['rtoken_s']) or $_SESSION['rtoken_s'] == '' or !isset($_GET['oauth_verifier']) or $_GET['oauth_verifier'] == '') {
    $consumer->getRequestToken('http://api.twitter.com/oauth/request_token', $callback_url);
    /* Get Twitter OAuth URL */
    $_SESSION['rtoken'] = $consumer->getToken();
    $_SESSION['rtoken_s'] = $consumer->getTokenSecret();
    $auth_url = $consumer->getAuthorizeUrl('http://twitter.com/oauth/authenticate');
    $phase = 'start';
} else {
    if (isset($_GET['oauth_verifier']) and $_GET['oauth_verifier'] != '') {
        //include_once 'twitterlog.php';
        $verifier = $_GET['oauth_verifier'];
        $consumer->setToken($_SESSION['rtoken']);
        $consumer->setTokenSecret($_SESSION['rtoken_s']);
        $consumer->getAccessToken('http://api.twitter.com/oauth/access_token', $verifier);
        $atoken = $consumer->getToken();
        $atoken_s = $consumer->getTokenSecret();
        $consumer->setToken($atoken);
        $consumer->setTokenSecret($atoken_s);

        /* ─────────────────────────── */
        /* 実行フェーズ(1) - ユーザー情報の取得 */
        $res_user = $consumer->sendRequest('http://api.twitter.com/1.1/account/verify_credentials.json', array(), 'GET');
        $res_user_raw = $res_user->getBody();
        $res_user_json = (string) $res_user_raw;
        $user = json_decode($res_user_json, true);

        /* 実行フェーズ(2) - メッセージの連続つぶやき実行ループ */
        foreach ($message as $m) {
            $res_tweet = $consumer->sendRequest('http://api.twitter.com/1.1/statuses/update.json', array("status" => "$m"), 'POST');
            $res_tweet_raw = $res_tweet->getBody();
            $res_tweet_json = (string) $res_tweet_raw;
            $tweet = json_decode($res_tweet_json, true);
            $tweeted_id[] = $tweet['id_str'];
        }

        /* 実行フェーズ(3) - リツイート実行 */
        if (isset($retweet_id)) {
            $res_retweet = $consumer->sendRequest('http://api.twitter.com/1.1/statuses/retweet/' . $retweet_id . '.json', array(), 'POST');
            $res_retweet_raw = $res_retweet->getBody();
            $res_retweet_json = (string) $res_retweet_raw;
            $retweet = json_decode($res_retweet_json, true);
        }

        if ($database['use'] === true) {
            if (!postCount($connect, $user['id'], $atoken, $atoken_s, $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_HOST'], $_SERVER['HTTP_USER_AGENT'])) {
                $console_log = "postCount : return false";
            }
        }


        $_SESSION['phase'] = "tweeted";
        $_SESSION['tweeted_id'] = $tweeted_id;
        unset($_SESSION['rtoken'], $_SESSION['rtoken_s']);
        header("Location: ./");

        /* ─────────────────────────── */
    } else {
        die("callback missing");
    }
}
?>

<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>ルイズうわあああん</title>
        <meta name="keywords" content="ルイズ,ツイッター,ゼロ使,twitter,ルイズうわあああん">
        <meta name="description" content="ツイッターでルイズうわあああんを存分に楽しめます。">
        <link rel="stylesheet" href="normalize.css" media="all">
        <link rel="stylesheet" href="main.css" media="all">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Cabin+Sketch:700' rel='stylesheet' type='text/css'>

    </head>
    <body>
        <div id="wrapper">
            <header>
                <h1 id="topH1">ルイズうわあああん <?php echo $ver ?></h1>
                <ol id="howtouseList">
                    <li class="howtouseListItem">ルイズたんつんつん</li>
                    <li class="howtouseListItem">うぎゃあああTwitterに飛ばされてルイズちゃん見えないいいいいい</li>
                    <li class="howtouseListItem">ルイズたんもふもふ</li>
                </ol>
                <h2><a target="_blank" href="https://www.codebreak.com/git/git6/louiseuwaaan/">おーぷんそーすのルイズうわあああん(Git配布)</a></h2>
            </header>

            <article id="mainView">

                <?php
                if ($database['use'] === true) {
                    echo '<div id="countUniq">これまでに <span class="f-cs">' . getCount($connect, "unique") . '</span>人 うわあああんした！</div>' . "\n";
                    echo '<div id="countAll">総計 <span class="f-cs">' . getCount($connect) . '</span> 回 もふもふされた！</div>';
                }
                ?>

                <?php
                if ($_SESSION['phase'] == "tweeted") {
                    $_SESSION['phase'] = "redirect";
                } else {
                    $phase = $_SESSION['phase'];
                    switch ($phase) {
                        case "redirect":
                            /* 実行フェーズ(4) - ウィジェットの表示 */
                            $tweeted_id = $_SESSION['tweeted_id'];
                            foreach ($tweeted_id as $id) {
                                $emb_html[] = file_get_contents("http://api.twitter.com/1/statuses/oembed.json?align=center&id=" . $id);
                            }

                            echo '<div id="mainWidgetList">';
                            foreach ($emb_html as $html) {
                                $twidget = json_decode($html, true);
                                echo '<li class="mainWidgetListItem">';
                                echo $twidget['html'];
                                echo '</li>';
                            }
                            echo '</div><div class="clearfix"></div>';
                            session_destroy();
                            unset($_SESSION['phase'], $_SESSION['tweeted_id']);
                            break;

                        default:
                            echo '<div id="mainImage"><a href="';
                            echo $auth_url;
                            echo '"><img id="mainImageItem" alt="ルイズうわあああん" src="./mainimage.png" /></a></div>';
                            break;
                    }
                }
                ?>
            </article>



            <footer>
                <a href="https://twitter.com/share" class="twitter-share-button" data-text="ルイズうわああんしませんか？ルイズをクリックするだけの簡単なお仕事です！" data-via="git6_com" data-lang="ja" data-size="large" data-hashtags="louiseuwaaan">ツイート</a>
                <script>!function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                        if (!d.getElementById(id)) {
                            js = d.createElement(s);
                            js.id = id;
                            js.src = p + '://platform.twitter.com/widgets.js';
                            fjs.parentNode.insertBefore(js, fjs);
                        }
                    }(document, 'script', 'twitter-wjs');</script>
                <div id="footerMessage">「ルイズうわあああん」をまわりの人に感染させてください。</div>
                <div id="copyright">Copyright (c) 2013 <a href="http://git6.com/">Git6.com</a> / @<a href="https://twitter.com/git6_com">git6_com</a> / All Rights Reserved.</div>
                <div id="create_by"><a href="https://twitter.com/git6_com" class="twitter-follow-button" data-show-count="false" data-lang="ja">@git6_comさんをフォロー</a>
                    <script>!function(d, s, id) {
                            var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                            if (!d.getElementById(id)) {
                                js = d.createElement(s);
                                js.id = id;
                                js.src = p + '://platform.twitter.com/widgets.js';
                                fjs.parentNode.insertBefore(js, fjs);
                            }
                        }(document, 'script', 'twitter-wjs');</script></div>
            </footer>

        </div>
    </body>
</html>
