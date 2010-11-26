<?php
	class RolesUsers extends CollectionDBTemplated
	{
		protected $DBTableName = 'tasks';
		protected $Forms = array(
		'list' => 'objects/rolesUsers/list_menu.html',
		'gant_line' => 'objects/task/gant_line.html',
		);

		protected static $SQLFields = array(
		'UserID' => 'UserID',
		'RoleID' => 'RoleID',
		'UserDescr' => 'UserDescr',
		'RoleDescr' => 'RoleDescr'
		);

		public function Refresh()
		{
			$sql = "SELECT 
			`users`.`ID` as UserID,
			`users`.`DESCRIPTION` as UserDescr,

			`role`.`ID` as RoleID,
			`role`.`DESCRIPTION` as RoleDescr

			FROM `user_roles`INNER JOIN 
			`role` ON `user_roles`.`RoleID` = `role`.`ID`  INNER JOIN
			`users` ON `user_roles`.`UserID` = `users`.`ID`";
			
			if (isset($this->ViewData['Filter']) and is_array($this->ViewData['Filter']))
			{
				$Conditions = '';
				foreach ($this->ViewData['Filter'] as $FilterRec)
				{
					$Conditions = $Conditions.($Conditions==''?'':' and ').$this->CreateQueryFilter($FilterRec);
				}
				if ($Conditions != '') $sql .= ' where '.$Conditions;
			}

			$hSql = $this->DataBase->Query($sql);

			while ($fetch = $this->DataBase->FetchObject($hSql)) 
			{
				$ClassName = $this->_ValueType;
				$this->add($ClassName::GetObject($null,$null,$this->DataBase,$fetch->UserID));
			}
		}

		protected function __construct($ProcessData,$ViewData,$DataBase,$ID)
		{

			parent::__construct($ProcessData,$ViewData,$DataBase,'User');
			$this->Refresh();
		}

		static public function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
		}
	
	}  
?>
