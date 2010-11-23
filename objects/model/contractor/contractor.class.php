<?php
	class Contractor extends Entity
	{
		protected $DBTableName = 'contractors';
		
		public static $UnAuthForms = array('edit'=>true);
		
		public $Description = "Description";
		
		public static $Forms = array(
		'edit' => 'objects/model/contractor/edit.html',
		'view' => 'objects/model/contractor/view.html',
		'userlist' => 'objects/model/contractor/userlist.html',
		);

		public function Save()
		{
			return true;
		}
		
		public function Delete()
		{
			
		}
		
		public function Refresh()
		{
			$null = null;
			if (intval($this->ID))
			{
				$sql = 'Select * from '.$this->DBTableName.' where ID = '.$this->ID;
				
				$hSql = DBMySQL::Query($sql);
				while ($fetch = DBMySQL::FetchObject($hSql)) 
				{
					$this->Description = $fetch->DESCRIPTION;
				}
			}
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
	
