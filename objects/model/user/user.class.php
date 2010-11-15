<?php
	class User extends Entity
	{
		public $Avatar='images/avatar-none.png';
		protected $DBTableName = 'users';
		public static   $Actions 		= array(
		'save'=>'SaveAction',
		'updatepassword'=>'UpdatePassword',
		'delete'=>'DeleteAction'
		);
		

		protected $Login;
		protected $Email;


		public static    $Forms = array(
		'avatar' => 'objects/user/avatar.html',
		'edit' => 'objects/user/edit.html',
		'view' => 'objects/user/view.html',
		);

		protected function SetActionData()
		{
			parent::SetActionData();
			if (isset($this->ProcessData['Login']) and ($this->ProcessData['Login'] != $this->Login))
			{
				$this->Login = $this->ProcessData['Login'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['Email']) and ($this->ProcessData['Email'] != $this->Email))
			{
				$this->Email = $this->ProcessData['Email'];
				$this->Modified = true;
			}
			return $this->Modified; 
		}

		protected function Refresh()
		{
			if (is_int($this->ID))
			{
				$sql = 'Select * from '.$this->DBTableName.' where ID = '.$this->ID;
				$hSql = $this->DataBase->Query($sql);
				while ($fetch = $this->DataBase->FetchObject($hSql)) 
				{
					$this->Description = $fetch->DESCRIPTION;
					$this->Login = $fetch->LOGIN;
					$this->Email = $fetch->EMAIL;
				}
			}
		}

		public function Save()
		{
			if (!is_int($this->ID))
			{
				$sql = 'insert into '.$this->DBTableName.' (ID, DESCRIPTION,LOGIN,EMAIL) values (NULL,"'.$this->Description.'","'.$this->Login.'","'.$this->Email.'")';
				$hSql = $this->DataBase->Query($sql);
				if ($hSql)
				{
					$this->ID = $this->DataBase->InsertID($hSql);
					$EH = ErrorHandle::GetInstance();
					$EH->ChangedFields[] = array('name' => 'ID','value' => $this->ID);
					$EH->ErrorHandle('Объект типа '.get_class($this).' успешно сохранен.',0);
					$Result = true;
				}
				else
				{
					ErrorHandle::ErrorHandle('Ошибка сохранения объекта типа '.get_class($this).'.',2);
					$Result = true;
				}
			}
			else
			{
				$sql = 'update '.$this->DBTableName.' set 
				DESCRIPTION="'.$this->Description.'", 
				LOGIN="'.$this->Login.'", 
				EMAIL="'.$this->Email.'" 
				where ID = '.$this->ID;
				$hSql = $this->DataBase->Query($sql);
				if ($hSql)
				{
					ErrorHandle::ErrorHandle('Объект типа '.get_class($this).' успешно сохранен.',0);
					$Result = true;
				}
				else
				{
					ErrorHandle::ErrorHandle('Ошибка сохранения объекта типа '.get_class($this).'.',2);
					$Result = true;
				}
			}
		}      

		public function UpdatePassword()
		{
			ErrorHandle::ErrorHandle('Новый пароль выслан на элекронную почту',0);
			return true;
		}   


		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			
		}

		static public function GetObject(&$ProcessData,$id=null)
		{
			return static::GetObjectInstance($ProcessData,$id,__CLASS__);
		}

	}

?>
