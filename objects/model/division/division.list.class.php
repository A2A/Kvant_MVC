<?php
	class DivisionList extends CollectionDB
	{
		protected $DBTableName = 'division';
		public static $Forms = array(
		'list' => 'objects/model/division/list.html'
		);

		public static $SQLFields = array(
		'ID' => 'ID',
        'Description' => 'DESCRIPTION',
        'ParentID' => 'PARENTID',
        'ManagerID' => 'MANAGERID'
		);
		
		public function __construct($ProcessData)
		{

			parent::__construct($ProcessData,'Division');
			$this->Refresh();
		}

		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
	
	}  
?>
