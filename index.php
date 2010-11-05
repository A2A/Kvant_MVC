<?php
	session_start();
	@   error_reporting (E_ALL);  

	header('Content-type: text/html; charset=utf-8',TRUE);   

	include_once('common_procs.php');     

	$null = null;
	$Res= Controller::Run();
	$Res = str_replace('<!--#error_field#-->',ErrorHandle::SystemStatusOutput(),$Res);  
	
	echo $Res;
?>
