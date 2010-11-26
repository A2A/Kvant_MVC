<?php
	class DRU extends Entity
	{
		protected $DBTableName = 'dru'; 

		public $UserID;
		public $RoleID;
		public $DivisionID;

		public $Color;

		public static $Forms = array(
		'edit' => 'objects/model/dru/edit.html',
		'description' => 'objects/model/dru/description.html'      
		);

		public static  $SQLFields = array(
		'ID' => 'ID',
		'DivisionID' => 'DIVISIONID',
		'RoleID' => 'ROLEID',
		'UserID' => 'USERID'
		);

		static public function GetSQLField($Field)
		{
			return DRU::$SQLFields[$Field];
		}

		public function Refresh()
		{
			if (intval($this->ID) >0)
			{
				$System = System::GetObject();

				$this->Modified = false;
				$sql_base = 'SELECT `DIVISIONID`,`ROLEID`,`USERID`,`ID`,`COLOR` FROM '.$this->DBTableName.' where ID = '.intval($this->ID);
				$sql_filter = 'select OBJECTID from ur_dru where ID = "'.$System->CurrentUserID.'" and `READ` and OBJECTID = '.intval($this->ID);
				$sql = 'Select buf.* from ('.$sql_base.') as buf cross join  ('.$sql_filter.') as perms on buf.ID =  perms.OBJECTID';          

				$hSql = DBMySQL::Query($sql);
				while ($fetch = DBMySQL::FetchObject($hSql)) 
				{
					$this->ID = $fetch->ID;
					$this->UserID = $fetch->USERID;
					$this->RoleID = $fetch->ROLEID;
					$this->DivisionID = $fetch->DIVISIONID;
					$this->Color = $fetch->COLOR;
					$this->Description = $this->DivDescr.'/'.$this->RoleDescr.'/'.$this->UserDescr;
				}
			}
		}

		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			$this->Refresh();     
		}

		public function GetParentDRU($DRUType)
		{
			$DCondition = (stripos($DRUType,'D')===false) ? ' DIVISIONID is null '  : ' DIVISIONID = "'.$this->DivisionID.'" ';
			$RCondition = (stripos($DRUType,'R')===false) ? ' ROLEID is null '      : ' ROLEID = "'.$this->RoleID.'" ';
			$UCondition = (stripos($DRUType,'U')===false) ? ' USERID is null '      : ' USERID = "'.$this->UserID.'" ';

			$Sql = 'select ID from dru where '.$DCondition.' and '.$RCondition.' and '.$UCondition;
			$hSql = $this->DataBase->Query($sql);
			if ($fetch = $this->DataBase->FetchObject($hSql)) 
			{
				$result = $fetch->ID;
			}
			else $result = null;
			return $result;

		}

		public function __get($FieldName)
		{
			$null = null;
			switch ($FieldName)
			{
				case 'UserDescr': {
									$result = ""; 
									if (is_numeric($this->UserID) and $this->UserID > 0)
									{
										$result = $this->User->Description;
									}
									else
											$result = 'Все сотрудники'; 
									}
									break;
				case 'RoleDescr': $result = is_null($this->RoleID)      ?'Все роли'         : ($this->Role->Description); break;
				case 'DivDescr': $result = is_null($this->DivisionID)   ?'Все подразделения': ($this->Division->Description); break;

				case 'User': $result = is_null($this->UserID)      ?null: User::GetObject($null,$this->UserID); break;
				case 'Role': $result = is_null($this->RoleID)      ?null: Role::GetObject($null,$this->RoleID); break;
				case 'Division': $result = is_null($this->DivisionID)   ?null: Division::GetObject($null,$this->DivisionID); break;
				
				case 'UserID': $result = $this->UserID; break;
				case 'RoleID': $result = $this->RoleID; break;
				case 'DivisionID': $result = $this->DivisionID; break;

				case 'DRUType': 
				{
					$d = is_null($this->DivisionID) ?'': 'D';
					$r = is_null($this->RoleID)     ?'': 'R';
					$u = is_null($this->UserID)     ?'': 'U';
					$result = $d.$r.$u; 
					break;
				}

				case 'DR_Parent'    : $result = GetParentDRU('DR'); break;
				case 'DU_Parent'    : $result = GetParentDRU('DU'); break;
				case 'RU_Parent'    : $result = GetParentDRU('RU'); break;
				case 'D_Parent'     : $result = GetParentDRU('D');  break;
				case 'R_Parent'     : $result = GetParentDRU('R');  break;
				case 'U_Parent'     : $result = GetParentDRU('U');  break;

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
	
