<?php
	class Division extends Entity
	{
		protected $DBTableName = 'division';    
        
        protected $ParentID;
		public static $Forms = array(
		'edit' => 'objects/model/division/edit.html'
		);

		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			$this->Refresh();     
		}
        
        protected function Refresh()
        {
            if (intval($this->ID) >0)
            {
                $this->Modified = false;
                $sql = 'Select * from '.$this->DBTableName.' where ID = '.intval($this->ID);
                $hSql = $this->DataBase->Query($sql);
                while ($fetch = $this->DataBase->FetchObject($hSql)) 
                {
                    $this->Description = $fetch->DESCRIPTION;
                    $this->ParentID = $fetch->PARENTID;
                }
            }
        }

        public function __get($FieldName)
        {
            switch ($FieldName)
            {
                case 'Parent' : $result = Division::GetObject(null,$this->ParentID);
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
	
