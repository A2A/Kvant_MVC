<?php
	session_start();
	@   error_reporting (E_ALL);  

	header('Content-type: text/html; charset=utf-8',TRUE);   

	include_once('common_procs.php');     

	$null = null;
    try
    {
        $Res= Controller::Run();
    }
    catch (Exception $e)
    {
        ErrorHandle::ErrorHandle($e->message,0);
    }
	$Res = str_replace('<!--#error_field#-->',ErrorHandle::SystemStatusOutput(),$Res);  
	
	echo $Res;
?>
