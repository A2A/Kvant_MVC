<?php
	class TpeClass extends Entity 
	{
		protected $DBTableName = 'tpe_class';    
		protected $Forms = array(
		'edit' => 'objects/tpe_class/edit.html'      
		);

		public $Dock = 'nodock'; 
				   
		protected static $SQLFields = array(
		'ID' => 'ID',
		'Description' => 'DESCRIPTION',
		'TypeID' => 'TYPEID'
		);
		
		public function __construct(&$ProcessData,&$ViewData,&$DataBase,$ID=null) 
		{
			parent::__construct($ProcessData,$ViewData,$DataBase,$ID);
			$this->Refresh(); 
			//print_r($this);  
		}

	
		static public function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
		}
		
	}  
?>
