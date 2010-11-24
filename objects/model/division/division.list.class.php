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

        protected function Refresh()
        {
            
            $System = System::GetObject();
            $null = null;
            $sql_base = 'Select ID from division ';
            if (isset($this->ProcessData['Filter']) and is_array($this->ProcessData['Filter']))
            {
                $Conditions = '';
                foreach ($this->ProcessData['Filter'] as $FilterRec)
                {
                    $Conditions = $Conditions.($Conditions==''?'':' and ').$this->CreateQueryFilter($FilterRec);
                }
                if ($Conditions != '') $sql .= ' where '.$Conditions; 
            
            }
            
            $sql = '
            Select temp_buf.* 
            from ('.$sql_base.' ) as temp_buf 
                cross join  (select OBJECTID from ur_division where ID = "'.$System->CurrentUserID.'") as right_filter
                on   temp_buf.ID =  right_filter.OBJECTID
            ';          
            
            if (!($hSql = DBMySQL::Query($sql)))
            {
                ErrorHandle::ErrorHandle("Ошибка при получении списка подразделений.");
            }
            else
            {
                while ($fetch = DBMySQL::FetchObject($hSql)) 
                {
                    $ClassName = $this->_ValueType;
                    if ($obj = $ClassName::GetObject($null,$fetch->ID))
                    {
                        $this->add($obj);
                    }
                }
            }
        }
        
		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
	
	}  
?>
