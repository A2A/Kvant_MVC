<?php
	class Role extends Entity
	{
		protected $DBTableName = 'roles';    
		protected $Forms = array(
		'edit' => 'objects/role/edit.html',
		'view' => 'objects/role/view.html',
		'UserList' => 'objects/role/userlist.html',
		);

		public function __construct(&$ProcessData,&$ViewData,&$DataBase,$ID=null)  
		{   
			parent::__construct($ProcessData,$ViewData,$DataBase,$ID);
			$this->Refresh();     
		}
		static public function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
		}
	}  
?>
	
