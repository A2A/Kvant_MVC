<?php
	class Role extends Entity
	{
		protected $DBTableName = 'roles';    
		public static $Forms = array(
		'edit' => 'objects/model/role/edit.html',
		'view' => 'objects/model/role/view.html',
		'UserList' => 'objects/model/role/userlist.html',
		);

		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			$this->Refresh();     
		}
		public function __get($FieldName)
		{
			switch ($FieldName)
			{
				case 'Active': $result = ($this->ID == $_SESSION['CurrentRoleID']?'Active':'NoActive'); break;
				default: $result = parent::__get($FieldName);
			}
			return $result;
		}
		
		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}
	}  
?>
	
