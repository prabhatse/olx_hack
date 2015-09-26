<?php
/*
$con = mysql_connect('localhost','root','abc123');
$db = mysql_select_db('test');


$q = "INSERT INTO notify ('user_id','msg') values ($user_id,$msg)";

mysql_query($q);
*/
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

echo $redis->ping();


$redis->lPush("key1e",1,2,3,4);

var_dump($redis->lRange('key1e',0,-1));


$key="Key_Name";
$redis->set($key, 'Key Value');
echo $redis->get($key);

//echo $_POST['time'];

?>
