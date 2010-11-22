<?php
	class EventList extends CollectionDB
	{
		protected $DBTableName = 'events';
		public static $Forms = array(
		'list_menu' => 'objects/model/event/list_menu.html',
		'list' => 'objects/model/event/list.html',
		'list_continue_event' => 'objects/model/event/list_continue_event.html',
		);

		 public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,'Event');
			$this->Refresh();
	 
		}
		
		
		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}


	}
?>
