<?php
	class DRUList extends CollectionDB
	{
		protected $DBTableName = 'dru';
		public static $Forms = array(
		'list' => 'objects/model/dru/list.html',
		'menu_current_user' => 'objects/model/dru/menu_current_user.html',
		'menu_user' => 'objects/model/dru/menu_user.html',
		'menu_duser_level2' => 'objects/model/dru/menu_duser_level2.html',
		'menu_ruser_level2' => 'objects/model/dru/menu_ruser_level2.html',
		'menu_user_level2' => 'objects/model/dru/menu_user_level2.html',
		'menu_user_level3' => 'objects/model/dru/menu_user_level3.html',
		'menu_division' => 'objects/model/dru/menu_division.html',
		'menu_division_level2' => 'objects/model/dru/menu_division_level2.html',
		'menu_udivision_level2' => 'objects/model/dru/menu_udivision_level2.html',
		'menu_rdivision_level2' => 'objects/model/dru/menu_rdivision_level2.html',
		'menu_division_level3' => 'objects/model/dru/menu_division_level3.html',
		'menu_role' => 'objects/model/dru/menu_role.html',
		'menu_role_level2' => 'objects/model/dru/menu_role_level2.html',
		'menu_urole_level2' => 'objects/model/dru/menu_urole_level2.html',
		'menu_drole_level2' => 'objects/model/dru/menu_drole_level2.html',
		'menu_role_level3' => 'objects/model/dru/menu_role_level3.html',
		'tpe_create' => 'objects/model/dru/tpe_create.html',
		
		'unit' => 'objects/model/dru/unit.html',
		'role' => 'objects/model/dru/role.html',
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
			
			if (isset($this->ProcessData['Filter']) and is_array($this->ProcessData['Filter']))
			{
				$Conditions = '';
				$group = '';
				foreach ($this->ProcessData['Filter'] as $FilterRec)
				{
					$Conditions = $Conditions.($Conditions==''?'':' and ').$this->CreateQueryFilter($FilterRec);
					if (isset($FilterRec['Group']) and $FilterRec['Group']=='1')
					{
						$group = DRU::GetSQLField($FilterRec['Field']);
					}
				}
				if ($Conditions != '') $sql_base .= ' where '.$Conditions;
				if ($group != '') $sql_base .= ' group by '.$group;
			}
			
			//echo $sql_base;
			
			$sql_filter = 'select OBJECTID from ur_DRU where ID = "'.$System->CurrentUserID.'" and `READ`';
			
			$sql = 'Select buf.* from ('.$sql_base.') as buf cross join  ('.$sql_filter.') as perms on buf.ID =  perms.OBJECTID';          
			//ErrorHandle::ErrorHandle($sql);  
			
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
