<?php
	class RolesUsers extends CollectionDBTemplated
	{
		protected $DBTableName = 'roles';
		protected $Forms = array(
		'list' => 'objects/role/list_menu.html',
		'gant_line' => 'objects/task/gant_line.html',
		'event_creat' => 'objects/role/event_creat.html',
		);

		protected static $SQLFields = array(
		'UserID' => 'UserID',
		'RoleID' => 'RoleID',
		'UserDescr' => 'UserDescr',
		'RoleDescr' => 'RoleDescr'
		);
		/*
		public function Refresh()
		{
			$sql = "SELECT 
			`users`.`ID` as UserID,
			`users`.`DESCRIPTION` as UserDescr,

			`roles`.`ID` as RoleID,
			`roles`.`DESCRIPTION` as RoleDescr

			FROM `user_roles`INNER JOIN 
			`roles` ON `user_roles`.`RoleID` = `roles`.`ID`  INNER JOIN
			`users` ON `user_roles`.`UserID` = `users`.`ID`";
			// TODO 3 -o Natali -c Заглушка: Заглушка заполнения
			$sql = "SELECT 
			`users`.`ID` as UserID,
			`users`.`DESCRIPTION` as UserDescr,

			`roles`.`ID` as RoleID,
			`roles`.`DESCRIPTION` as RoleDescr

			FROM `roles`";
			
			// DONE 5 -o Molev -c Category: Написать рефреш

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
		*/

		public function __construct($ProcessData,$ViewData,$DataBase)
		{

			parent::__construct($ProcessData,$ViewData,$DataBase,'Role');
			$this->Refresh();
		}

		static public function GetObject(&$ProcessData,&$ViewData,&$DataBase,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$ViewData,$DataBase,$id,__CLASS__);
		}
	
	}  
?>
