<?php
	class RoleList extends CollectionDB
	{
		protected $DBTableName = 'roles';
		public static $Forms = array(
		'list' => 'objects/role/list_menu.html',
		'rolelistwithactive' => 'objects/model/role/roles_list_with_active.html',
		'event_creat' => 'objects/role/event_creat.html',
		);

		public static $SQLFields = array(
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
        protected function Refresh()
        {
            $System = System::GetObject();
            $null = null;
            $sql = '
SELECT 
  `roles` .`ID`,
  `roles`.`DESCRIPTION`
FROM 
  `roles` CROSS JOIN `dru` on `dru`.`ROLEID` = `roles`.`ID` and `dru`.`USERID`="'.$System->CurrentUserID.'"';
            
            if (isset($this->ViewData['Filter']) and is_array($this->ViewData['Filter']))
            {
                $Conditions = '';
                foreach ($this->ViewData['Filter'] as $FilterRec)
                {
                    $Conditions = $Conditions.($Conditions==''?'':' and ').$this->CreateQueryFilter($FilterRec);
                }
                if ($Conditions != '') $sql .= ' where '.$Conditions;
            }
            // TODO 10 -o N -c Сообщение для отладки: SQL
                ErrorHandle::ErrorHandle($sql);

            $hSql = DBMySQL::Query($sql);
            while ($fetch = DBMySQL::FetchObject($hSql)) 
            {
                $ClassName = $this->_ValueType;
                $this->add($ClassName::GetObject($null,$fetch->ID));
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
