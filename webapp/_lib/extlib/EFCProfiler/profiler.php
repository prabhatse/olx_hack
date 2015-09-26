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

require_once 'class.Loader.php';
Loader::register();


$logger = new EFCLogger();

//var_dump($logger);exit;

$profiler = new EFCProfiler($logger);

  $profiler->startTimer('testLogging');


$object = $_SERVER;

$logger->debug($object);
//$logger->info('Hello World!');
//$logger->notice('Some event occurred.');
//$logger->warning('Careful: some warning.');
//$logger->error('Runtime error.');
//$logger->critical('This needs to be fixed now!');
//$logger->emergency('The website is down right now.');
sleep(2);
$profiler->endTimer('testLogging');

//echo $profiler;

/*
define("HOST", "192");
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
foreach( $get_result as $val ){
}
}else{
 
//Set the table name according to Your Table
$query = "select * from notify WHERE id=1";
$result = mysql_query($query) or die('Error in Query!');
 
 
$getAllResult = array();
while( $row = mysql_fetch_array($result) ){
 
//Please set the field name according to your Table
$getAllResult[] = $row['msg'];
}
 
//Set Data In Cache for 20 Sec. or set 0 to store lifetime
$memc->set($key, $getAllResult, MEMCACHE_COMPRESSED, 20);
mysql_free_result($result);
}
}

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

$assetPath = 'assets/';
*/
?>
<html>
<head>
<link rel='stylesheet' type="text/css" href='assets/profiler.css'>
</head>
<body>
<!-- ANBU - LARAVEL PROFILER -->

<div class="anbu">
	<div class="anbu-window">
		<div class="anbu-content-area">
			<div class="anbu-tab-pane anbu-table anbu-log">
				<?php if(count($logger->getLogs()) > 0): ?>
					<table>
						<tr>
							<th>Type</th>
							<th>Message</th>
						</tr>
						<?php foreach($logger->getLogs() as $log): ?>
							<tr>
								<td class="anbu-table-first">
									<?php echo $log['level']; ?>
								</td>
								<td>
									<?php echo $log['message']; ?>
								</td>
						<?php endforeach; ?>
						</tr>
					</table>
				<?php else: ?>
					<span class="anbu-empty">There are no log entries.</span>				
				<?php endif; ?>
			</div>

			<div class="anbu-tab-pane anbu-table anbu-sql">
				<?php if (count($logger->getQueries()) > 0): ?>
					<table>
						<tr>
							<th>Time</th>
							<th>Query</th>
						</tr>
						<?php foreach($logger->getQueries() as $query): ?>
							<tr>
								<td class="anbu-table-first">
									<?php echo $query['time']; ?>ms
								</td>
								<td>
									<pre><?php echo $query['query']; ?></pre>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				<?php else: ?>
					<span class="anbu-empty">There have been no SQL queries executed.</span>
				<?php endif; ?>
			</div>

			<div class="anbu-tab-pane anbu-table anbu-checkpoints">
				<table>
					<tr>
						<th>Name</th>
						<th>Running Time (ms)</th>
					</tr>
					<?php foreach($profiler->getTimers() as $name => $timer): ?>
					<tr>
						<td class="anbu-table-first">
							<?php echo $name; ?>
						</td>
						<td><pre><?php echo $timer->getElapsedTime(); ?>ms</pre></td>
						<td>&nbsp;</td>
					</tr>
					
					<?php endforeach; ?>
				</table>
			</div>
			<div class="anbu-tab-pane anbu-table anbu-filecount">
				<table>
					<tr>
						<th>File</th>
						<th>Size</th>
					</tr>
					<?php foreach($profiler->getIncludedFiles() as $file): ?>
					<tr>
						<td class="anbu-table-first-wide"><?php echo $file['filePath']; ?></td>
						<td><pre><?php echo $file['size']?></pre></td>
						<td>&nbsp;</td>
					</tr>
					
					<?php endforeach; ?>
				</table>
			</div>			
		</div>
	</div>

	<ul id="anbu-open-tabs" class="anbu-tabs">
		<li><a data-anbu-tab="anbu-log" class="anbu-tab" href="#">Log <span class="anbu-count"><?php echo count($logger->getLogs()); ?></span></a></li>
		<li>
			<a data-anbu-tab="anbu-sql" class="anbu-tab" href="#">SQL 
				<span class="anbu-count"><?php echo count($logger->getQueries()); ?></span>
				<?php if(count($logger->getQueries()) > 0): ?>
					<span class="anbu-count"><?php echo array_sum(array_map(function($q) { return $q['time']; }, $logger->getQueries())); ?>ms</span>
				<?php endif; ?>
			</a>
		</li>
		<li><a class="anbu-tab" data-anbu-tab="anbu-checkpoints">Time <span class="anbu-count"><?php echo $profiler->getLoadTime(); ?>ms</span></a></li>
		<li><a class="anbu-tab">Memory <span class="anbu-count"><?php echo $profiler->getMemoryUsage(); ?> (<?php echo $profiler->getMemoryPeak(); ?>)</span></a></li>
		<li><a class="anbu-tab" data-anbu-tab="anbu-filecount">Files <span class="anbu-count"><?php echo count($profiler->getIncludedFiles()); ?></span></a></li>        
		<li class="anbu-tab-right"><a id="anbu-hide" href="#">&#8614;</a></li>
		<li class="anbu-tab-right"><a id="anbu-close" href="#">&times;</a></li>
		<li class="anbu-tab-right"><a id="anbu-zoom" href="#">&#8645;</a></li>
	</ul>

	<ul id="anbu-closed-tabs" class="anbu-tabs">
		<li><a id="anbu-show" href="#">&#8612;</a></li>
	</ul>
</div>

<script src='//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
<script src='assets/profiler.js'></script>

<!-- /ANBU - LARAVEL PROFILER -->

</body>
</html>
