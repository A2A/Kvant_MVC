<?php
	class Filter extends Entity 
	{
		protected $DBTableName = '';    
		protected $Forms = array(
		'entity' => 'objects/filter/entity.html'      
		);

		public $Dock = 'nodock'; 
				   
		protected static $SQLFields = array(
		'ID' => 'ID',
		'Description' => 'DESCRIPTION',
		'TypeID' => 'TYPEID'
		);
		
		protected static $SQLSubObjectTable = array(
		'event_types' => 'tpe_types',
		'task_types' => 'tpe_types',
		'project_types' => 'tpe_types',
		'event' => 'events',
		'task' => 'tasks',
		'project' => 'projects',
		);
		
		public function __construct(&$ProcessData,&$ViewData,&$DataBase,$ID=null) 
		{
			$this->DBTableName = $ViewData['SubObject'];    
			parent::__construct($ProcessData,$ViewData,$DataBase,$ID);
			$this->Refresh();  
			//print_r($this);
		}

	
		static public function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
		{
			//return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
			$ClassName = __CLASS__;
			return new $ClassName($ProcessData,$ViewData,$DataBase,$id); 
		}
		
	}  
?>
