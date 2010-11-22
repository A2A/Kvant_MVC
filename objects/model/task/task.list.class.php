<?php
	class TaskList extends CollectionDB
	{
		protected $DBTableName = 'tasks';
		public static  $Forms = array(
		'list_menu' => 'objects/model/task/list_menu.html',
		'gant_line' => 'objects/model/task/gant_line.html',
		);

		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,'Task');
			$this->Refresh();
	 
		}
		
		
		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}

	}  
?>
