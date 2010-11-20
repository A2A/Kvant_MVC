<?php
	class TpeClass extends Entity 
	{
		protected $DBTableName = 'tpe_class';    
		public static $Forms = array(
		'edit' => 'objects/model/tpe_class/edit.html'      
		);

		public $Dock = 'nodock'; 

		protected static $SQLFields = array(
		'ID' => 'ID',
		'Description' => 'DESCRIPTION',
		'TypeID' => 'TYPEID'
		);

		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			$this->Refresh();     
		}

		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}

	}  
?>
