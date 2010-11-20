<?php
	class DRU extends Entity
	{
		protected $DBTableName = 'dru';    
		public static $Forms = array(
		'edit' => 'objects/model/dru/edit.html',
		'view' => 'objects/model/dru/view.html',
		'UserList' => 'objects/model/dru/userlist.html',
		);

        public function Refresh()
        {
            
        }
        
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
	
