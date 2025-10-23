<?php
if(!isset($_SESSION['user_id'])){
    echo("请先登陆！");
    header("Location: login.php");
}
function panel()
{
    $base_avatar_dir="./../avatars/";
    $base_word_dir="./../words/";
    $avatar = include_once $base_avatar_dir.$_SESSION['user_id'].".png";
    $nickname = $_SESSION['nickname'];
    $words=include_once $base_word_dir.$_SESSION['user_id'].".txt";
}

function changeUserinfo(){

}
