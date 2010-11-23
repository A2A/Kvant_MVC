<?php
	class DRUList extends CollectionDB
	{
		protected $DBTableName = 'dru';
		public static $Forms = array(
		'list' => 'objects/model/dru/list.html',
		'menu_current_user' => 'objects/model/dru/menu_current_user.html',
		'menu_user' => 'objects/model/dru/menu_user.html',
		'menu_division' => 'objects/model/dru/menu_division.html',
		'menu_role' => 'objects/model/dru/menu_role.html',
		'tpe_create' => 'objects/model/dru/tpe_create.html',
		);

		public static $SQLFields = array(
		'ID' => 'ID',
		'Description' => 'DESCRIPTION',
		'ParentID' => 'PARENTID',
		'DivisionID' => 'DIVISIONID',
		'RoleID' => 'ROLEID',
		'UserID' => 'USERID'
		
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
