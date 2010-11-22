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

		public function Refresh()
		{
			if (intval($this->ID) >0)
			{
				$this->Modified = false;
				$sql = 'SELECT `DIVISIONID`,`ROLEID`,`USERID`,`ID`,`COLOR` FROM '.$this->DBTableName.' where ID = '.intval($this->ID);
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
				case 'UserDescr': $result = is_null($this->UserID)      ?'Все сотрудники'   : (''.User::GetObject($null,$this->UserID)); break;
				case 'RoleDescr': $result = is_null($this->RoleID)      ?'Все роли'         : (''.Role::GetObject($null,$this->RoleID)); break;
				case 'DivDescr': $result = is_null($this->DivisionID)   ?'Все подразделения': (''.Division::GetObject($null,$this->DivisionID)); break;

				case 'User': $result = is_null($this->UserID)      ?null: User::GetObject($null,$this->UserID); break;
				case 'Role': $result = is_null($this->RoleID)      ?null: Role::GetObject($null,$this->RoleID); break;
				case 'Div': $result = is_null($this->DivisionID)   ?null: Division::GetObject($null,$this->DivisionID); break;
				
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
	
