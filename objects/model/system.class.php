<?php
	class System extends BaseClass
	{
		public static   $UnAuthUse      = true;
		
		public static   $UnAuthActions  = array();
		public static   $Actions 		= array();
		
		public static   $UnAuthForms  	= array('default'=>true);
		public static   $Forms			= array('default'=>'index.html');
		
		public $title = "test";
		
		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}

	 
	}
  
?>
