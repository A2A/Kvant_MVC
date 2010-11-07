<?php
	class Object_template extends Entity
	{
		protected $DBTableName = 'role';
		
		public static $UnAuthForms = array('edit'=>true);
		
		public static $Forms = array(
		'edit' => 'objects/model/object_template/edit.html',
		'view' => 'objects/model/object_template/view.html',
		'userlist' => 'objects/model/object_template/userlist.html',
		);

		public function Save()
		{
			return true;
		}
		
		public function Delete()
		{
			
		}
		
		protected function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			$this->Refresh();
		}
		
		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
	}  
?>
	
