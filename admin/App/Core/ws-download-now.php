<?php
	include($_SERVER["DOCUMENT_ROOT"].'/admin/App/Lib/class-ws-v1.php');
	$file_url = ROOT_WEBSITE.'/'.$_GET['filename'];
	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
	readfile($file_url); 
?>