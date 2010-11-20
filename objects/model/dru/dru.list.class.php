<?php
	class DRUList extends CollectionDB
	{
		protected $DBTableName = 'dru';
		public static $Forms = array(
		'list' => 'objects/model/dru/list.html'
		);

		public static $SQLFields = array(
		'ID' => 'ID',
        'Description' => 'DESCRIPTION',
        'ParentID' => 'PARENTID'
		);
		
		public function __construct($ProcessData)
		{

			parent::__construct($ProcessData,'DRU');
			$this->Refresh();
		}

		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
	
	}  
?>
