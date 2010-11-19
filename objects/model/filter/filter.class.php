<?php
	class Filter extends Entity 
	{
		protected $DBTableName = '';    
		public static $Forms = array(
		'entity' => 'objects/model/filter/entity.html'      
		);

		public $Dock = 'nodock'; 

		public static $SQLFields = array(
		'ID' => 'ID',
		'Description' => 'DESCRIPTION',
		'TypeID' => 'TYPEID'
		);

		public static $SQLSubObjectTable = array(
		'event_types' => 'tpe_types',
		'task_types' => 'tpe_types',
		'project_types' => 'tpe_types',
		'event' => 'events',
		'task' => 'tasks',
		'project' => 'projects',
		);

		public function __construct(&$ProcessData,$ID=null)  
		{   
			//$this->DBTableName = $ViewData['SubObject'];   
			$this->DBTableName = 'tpe_types';
			parent::__construct($ProcessData,$ID);
			$this->Refresh();     
			
		}


		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}
	}  
?>
