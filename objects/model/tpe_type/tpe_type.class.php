<?php
	class TPEType extends Entity 
	{
		protected $DBTableName = 'tpe_types';    
		public static $Forms = array(
		'edit' => 'objects/model/tpe_type/edit.html',      
		'description' => 'objects/model/tpe_type/description.html'      
		);

		public $Dock = 'nodock'; 

		public static $SQLFields = array(
		'ID' => 'ID',
		'Description' 	=> 'DESCRIPTION',
		'ProjectUse' 	=> 'PROJECTUSE',
		'TaskUse' 		=> 'TASKUSE',
		'EventUse'		=> 'EVENTUSE'
		);

		public static function GetSQLField($Field)
		{
			// TODO 100 -o N -c Test SQL: delete
			ErrorHandle::ErrorHandle($Field."==".TPEType::$SQLFields[$Field]); 
			return TPEType::$SQLFields[$Field];
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
