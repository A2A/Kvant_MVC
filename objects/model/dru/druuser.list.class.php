<?php
	class DRUUserList extends CollectionDB
	{
		protected $DBTableName = 'dru';
		public static $Forms = array(
		'unit' => 'objects/model/dru/unit.html',
		'unit2' => 'objects/model/dru/unit2.html',
		'user' => 'objects/model/dru/user.html',
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
			$null = null;
			$sql_base = 'select dru.ID as ID from '.$this->DBTableName;
			$Conditions = '';
			if (isset($this->ProcessData['Filter']) and is_array($this->ProcessData['Filter']))
			{
				foreach ($this->ProcessData['Filter'] as $FilterRec)
				{
				
					if ($FilterRec['Field'] == 'ParentID' and $FilterRec['Oper'] == 'eq' and is_numeric($FilterRec['Val']))
					{
							$Condition = "dru.DIVISIONID = (select dru.DIVISIONID from dru where dru.ID = ".intval($FilterRec['Val']).")"; 
							
						
							
					}
					elseif ($FilterRec['Field'] == 'RoleID' and $FilterRec['Oper'] == 'eq' and is_numeric($FilterRec['Val']))
					{
						$Condition = "dru.ROLEID =  (select dru.ROLEID from dru where dru.ID = ".intval($FilterRec['Val']).")";
				
					}
					else
					{
						$Condition = "";
					}
					
					if($Condition != "") $Conditions = $Conditions.($Conditions==''?'':' and ').$Condition;

				}
				if ($Conditions != '') 
				{
					$sql_base .= ' where '.$Conditions."and dru.USERID is not null"; ;
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
