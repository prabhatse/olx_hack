<?php
/*
 * Copyright 2014 Empodex PHP Framework.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2014-2015 Empoddy Labs.
 * @author Prabhat Shankar <prabhat.singh88[at]gmail.com>
 */
// Connect into database
// Change the DB configuration according to you
 
define("HOST", "localhost");
define("USER", "root");
define("PASS", "abc123");
define("DBNAME", "test");
 
$conn = mysql_connect(HOST, USER, PASS);
 
if($conn){
mysql_select_db(DBNAME, $conn);
}
else{
die('Error in Connection!');
}
 
$memc = new Memcache;
$memc->connect('127.0.0.1', 11211) or die('Error in Connection!');
 
//Set the table name according to Your Table
$start = microtime(true);
for ($i = 0;$i < 10; $i++){
$key = md5("select * from notify WHERE id=1");
$get_result = array();
 
$get_result = $memc->get($key);
if($get_result){
echo 'From Memory:<br><br>';
foreach( $get_result as $val ){
echo 'Name: ' . $val . '<br>';
}
}else{
 
//Set the table name according to Your Table
$query = "select * from notify WHERE id=1";
$result = mysql_query($query) or die('Error in Query!');
 
echo 'From Database:<br><br>';
 
$getAllResult = array();
while( $row = mysql_fetch_array($result) ){
 
//Please set the field name according to your Table
echo 'Name: ' . $row['msg'] . '<br>';
$getAllResult[] = $row['msg'];
}
 
//Set Data In Cache for 20 Sec. or set 0 to store lifetime
$memc->set($key, $getAllResult, MEMCACHE_COMPRESSED, 20);
mysql_free_result($result);
}
}
$end = microtime(true);

echo $end-$start." Time. 2nd ";

$start = $end;

for ($i = 0;$i < 10; $i++){
    $query = "select * from notify WHERE id=1";
    $result = mysql_query($query) or die('Error in Query!');
$getAllResult = array();
while( $row = mysql_fetch_array($result) ){
 
//Please set the field name according to your Table
//echo 'Name: ' . $row['name'] . '<br>';
$getAllResult[] = $row['name'];
}
 
//Set Data In Cache for 20 Sec. or set 0 to store lifetime
//mysql_free_result($result);
}




$end = microtime(true);

echo $end-$start." Time";
