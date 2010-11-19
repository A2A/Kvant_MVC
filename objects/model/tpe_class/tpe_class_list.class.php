<?php
	class TpeClassList extends CollectionDBTemplated
	{
		protected $DBTableName = 'tpe_class';    
		protected $Forms = array(
		'list_select' => 'objects/tpe_class/select.html',
		'list' => 'objects/tpe_class/tpe_class.html',
		);

		
		public function __construct($ProcessData,$ViewData,$DataBase)
		{
			parent::__construct($ProcessData,$ViewData,$DataBase,'TpeClass');
			$this->Refresh();
			
		}
		
		
		static public function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
		}
		
	}  
?>
