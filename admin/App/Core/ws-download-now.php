<?php
	include(__DIR__.'/../Lib/class-ws-v1.php');
	$file_url = ROOT_WEBSITE.'/'.$_GET['filename'];
	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
	readfile($file_url); 
?>