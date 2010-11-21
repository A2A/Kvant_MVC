<?php
	class EventTypeList extends CollectionDBTemplated
	{
		protected $DBTableName = 'event_types';
		protected $Forms = array(
		'list' => 'objects/event/list_type_page.html',
		'list_select' => 'objects/event/list_type_select.html',
		);

		
		public function __construct($ProcessData,$ViewData,$DataBase)
		{
			parent::__construct($ProcessData,$ViewData,$DataBase,'EventType');
			$this->Refresh();
		}
		
		static public function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
		}

	}
?>
