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
		
        protected function Refresh()
        {
            $System = System::GetObject();
            $null = null;
            $sql_base = 'SELECT `ID` FROM `dru`';
            
            if (isset($this->ViewData['Filter']) and is_array($this->ViewData['Filter']))
            {
                $Conditions = '';
                foreach ($this->ViewData['Filter'] as $FilterRec)
                {
                    $Conditions = $Conditions.($Conditions==''?'':' and ').$this->CreateQueryFilter($FilterRec);
                }
                if ($Conditions != '') $sql .= ' where '.$Conditions;
            }

            $sql_filter = 'select OBJECTID from ur_DRU where ID = "'.$System->CurrentUserID.'" and `READ`';
            
            $sql = 'Select buf.* from ('.$sql_base.') as buf cross join  ('.$sql_filter.') as perms on buf.ID =  perms.OBJECTID';          
            
            if (!($hSql = DBMySQL::Query($sql)))
            {
                ErrorHandle::ErrorHandle("Ошибка при получении списка DRU.");
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
