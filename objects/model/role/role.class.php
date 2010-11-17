<?php
	class Role extends Entity
	{
		protected $DBTableName = 'roles';    
		public static $Forms = array(
		'edit' => 'objects/role/edit.html',
		'view' => 'objects/role/view.html',
		'UserList' => 'objects/role/userlist.html',
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
                case 'Active': return ($this->ID == $_SESSION['CurrentRoleID']?'Active':'NoActive'); break;
                default: parent::__get($FieldName);
            }
        }
        
		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}
	}  
?>
	