<?php
	class Role extends Entity
	{
		protected $DBTableName = 'roles';    
		public static $Forms = array(
		'edit' => 'objects/model/role/edit.html',
		'view' => 'objects/model/role/view.html',
		'UserList' => 'objects/model/role/userlist.html',
		);

		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			$this->Refresh();     
		}

        protected function Refresh()
        {
            $System = System::GetObject();
            if (intval($this->ID) >0)
            {
                $this->Modified = false;
                $sql_base = 'Select * from roles  where ID = '.intval($this->ID);
                $sql_filter = 'select OBJECTID from ur_roles where ID = "'.$System->CurrentUserID.'" and `READ` and OBJECTID = '.intval($this->ID);

                $sql = 'Select buf.* from ('.$sql_base.') as buf cross join  ('.$sql_filter.') as perms on buf.ID =  perms.OBJECTID';          
                if (!($hSql = DBMySQL::Query($sql)))
                {
                    ErrorHandle::ErrorHandle('Ошибка при получении данных о роли № '.$this->ID,1);
                }
                elseif (!($fetch = DBMySQL::FetchObject($hSql)))
                {
                    ErrorHandle::ErrorHandle('Попытка получения несуществующей роли или недостаточно прав на просмотр роли №'.$this->ID,1);
                }
                else
                {
                    $this->Description = $fetch->DESCRIPTION;
                }
            }
        }


		public function __get($FieldName)
		{
			switch ($FieldName)
			{
				case 'Active': $result = ($this->ID == $_SESSION['CurrentRoleID']?'Active':'NoActive'); break;
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
	
