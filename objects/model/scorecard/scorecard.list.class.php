<?php
	class ScoreCardList extends CollectionDB
	{
		protected $DBTableName = 'roles';
		public static $Forms = array(
		'list' => 'objects/model/scorecard/list.html',
		);

		public static $SQLFields = array(
		'UserID' => 'UserID',
		'RoleID' => 'RoleID',
		'UserDescr' => 'UserDescr',
		'RoleDescr' => 'RoleDescr'
		);

		protected function Refresh()
		{
			$System = System::GetObject();
			$null = null;
			$sql_base = 'SELECT `ID` FROM `roles`';
			
			if (isset($this->ViewData['Filter']) and is_array($this->ViewData['Filter']))
			{
				$Conditions = '';
				foreach ($this->ViewData['Filter'] as $FilterRec)
				{
					$Conditions = $Conditions.($Conditions==''?'':' and ').$this->CreateQueryFilter($FilterRec);
				}
				if ($Conditions != '') $sql .= ' where '.$Conditions;
			}

			$sql_filter = 'select OBJECTID from ur_roles where ID = "'.$System->CurrentUserID.'" and `READ`';
			
			$sql = 'Select buf.* from ('.$sql_base.') as buf cross join  ('.$sql_filter.') as perms on buf.ID =  perms.OBJECTID';          
			
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

		public function __construct($ProcessData)
		{

			parent::__construct($ProcessData,'Role');
			$this->Refresh();
		}

		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}
	
	}  
?>
