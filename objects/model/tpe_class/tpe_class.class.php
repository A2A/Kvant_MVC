<?php
	class TPEClass extends Entity 
	{
		protected $DBTableName = 'tpe_class';    
		public static $Forms = array(
		'edit' => 'objects/model/tpe_class/edit.html',      
		'description' => 'objects/model/tpe_class/description.html'      
		);

		public $Dock = 'nodock'; 

		public static $SQLField = array(
		'ID' => 'ID',
		'Description' => 'DESCRIPTION',
		'TypeID' => 'TYPEID'
		);

		public static function GetSQLField($Field)
		{
			 return TpeClass::$SQLField[$Field];
		}
		
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
