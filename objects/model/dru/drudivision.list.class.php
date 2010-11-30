<?php
	class DRUDivisionList extends CollectionDB
	{
		protected $DBTableName = 'dru';
		public static $Forms = array(
		'unit' => 'objects/model/dru/unit.html',
		'unit2' => 'objects/model/dru/unit2.html',
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
			$sql_base = 'select dru.ID as ID from  '.$this->DBTableName;
			$Conditions = '';
			if (isset($this->ProcessData['Filter']) and is_array($this->ProcessData['Filter']))
			{
				foreach ($this->ProcessData['Filter'] as $FilterRec)
				{
					if ($FilterRec['Field'] == 'RoleID' and $FilterRec['Oper'] == '!eq' and (!isset($FilterRec['Val']) or trim($FilterRec['Val']) == '')) 
					{   
						$this->DBTableName= "roles";
						$sql_base = 'select dru.ID as ID
						from  '.$this->DBTableName.'
						left join dru
						on dru.ROLEID = '.$this->DBTableName.'.ID';
					}
					elseif($this->DBTableName == "dru")
					{
						$this->DBTableName= "division"; 
						$sql_base = 'select dru.ID as ID
						from  '.$this->DBTableName.'
						left join dru
						on dru.DIVISIONID = '.$this->DBTableName.'.ID';
					}
				}    
					
				foreach ($this->ProcessData['Filter'] as $FilterRec)
				{
				
					if ($FilterRec['Field'] == 'ParentID' and $FilterRec['Oper'] == 'eq' and is_numeric($FilterRec['Val']))
					{
						if($this->DBTableName == "division") 
							$Condition = "division.PARENTID = (select dru.DIVISIONID from dru where dru.ID = ".intval($FilterRec['Val']).") 
							and dru.USERID is null";
						elseif($this->DBTableName == "roles")
							$Condition = "dru.DIVISIONID = (select dru.DIVISIONID from dru where dru.ID = ".intval($FilterRec['Val']).")	
							and dru.USERID is null";
					}
					else
					{
						$Condition = $this->CreateQueryFilter($FilterRec);
				
					}
					
					$Conditions = $Conditions.($Conditions==''?'':' and ').$Condition;

				}
				if ($Conditions != '') 
				{
					$sql_base .= ' where '.$Conditions;
				}
			}
			
			
			echo $sql_base;
			
			//$sql_filter = 'select OBJECTID from ur_DRU where ID = "'.$System->CurrentUserID.'" and `READ`';
			
			$sql = $sql_base; //'Select buf.* from ('.$sql_base.') as buf cross join  ('.$sql_filter.') as perms on buf.ID =  perms.OBJECTID';          
			//ErrorHandle::ErrorHandle($sql);  
			
			if (!($hSql = DBMySQL::Query($sql)))
			{
				ErrorHandle::ErrorHandle("Ошибка при получении списка DRU.");
				echo "Ошибка при получении списка DRU.<hr>";   
			}
			else
			{
				echo $ClassName = "DRU";  
				while ($fetch = DBMySQL::FetchObject($hSql)) 
				{
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
