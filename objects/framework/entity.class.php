<?php
	class Entity extends BaseClass
	{
		public static   $UnAuthUse      = true;
		
		public static   $UnAuthActions  = array();
		public static   $Actions 		= array(
		'save'=>'SaveAction',
		'delete'=>'DeleteAction'
		);
		
		public static   $UnAuthForms  	= array();

		public static$Forms = array('View'=>'','Edit'=>'');

		protected static $SQLFields = array(
		'ID' => 'ID',
		'Description' => 'DESCRIPTION'
		);

		protected $DBTableName;

		public $ID = null;
		public $Description = '';
		protected $Modified = false;
		protected $SystemMark = false;

		
		protected function Refresh()
		{
			if (intval($this->ID) >0)
			{
				$this->Modified = false;
				$sql = 'Select * from '.$this->DBTableName.' where ID = '.intval($this->ID);
				$hSql = DBMySQL::Query($sql);
				while ($fetch = DBMySQL::FetchObject($hSql)) 
				{
					$this->Description = $fetch->DESCRIPTION;
				}
			}
		}

		public function Save()
		{
			if (!is_int($this->ID))
			{
				$sql = 'insert into '.$this->DBTableName.' (ID, Description) values (NULL,"'.$this->Description.'")';
				$hSql = DBMySQL::Query($sql);
				if ($hSql)
				{
					$this->ID = DBMySQL::InsertID($hSql);
					$this->ChangedFields[] = array('name' => 'ID','value' => $this->ID);
					ErrorHandle::ActionErrorHandle('Объект типа '.get_class($this).' успешно сохранен.',0);
					$Result = true;
				}
				else
				{
					ErrorHandle::ActionErrorHandle('Ошибка сохранения объекта типа '.get_class($this).'.',2);
					$Result = false;
				}
			}
			else
			{
				$sql = 'update '.$this->DBTableName.' set Description="'.$this->Description.'" where ID = '.$this->ID;
				$hSql = DBMySQL::Query($sql);
				if ($hSql)
				{
					ErrorHandle::ActionErrorHandle('Объект типа '.get_class($this).' успешно сохранен.',0);
					$Result = true;
				}
				else
				{
					ErrorHandle::ActionErrorHandle('Ошибка сохранения объекта типа '.get_class($this).'.',2);
					$Result = false;
				}
			}
		}         

		public function Delete()
		{
			if (is_int($this->ID) and (!$this->SystemMark))
			{
				$sql = 'delete from '.$this->DBTableName.' where ID = '.$this->ID;
				$hSql = DBMySQL::Query($sql);
				if ($hSql)
				{
					ErrorHandle::ActionErrorHandle('Объект типа '.get_class($this).' успешно удален.',0);
					$Result = true;
				}
				else
				{
					ErrorHandle::ActionErrorHandle('Ошибка удаления объекта типа '.get_class($this).'.',3);
					$Result = false;
				}
			}
			elseif ($this->SystemMark)
			{
				ErrorHandle::ActionErrorHandle('Попытка удаления системного объекта типа '.get_class($this).'.',1);
				$Result = false;
			}
			else
			{
				ErrorHandle::ActionErrorHandle('Попытка удаления непринициализированного объекта типа '.get_class($this).'.',1);
				$Result = false;
			}
		}

		protected function SetActionData()
		{
			if (isset($this->ProcessData['Description']))
			{
				$this->Description = $this->ProcessData['Description'];
				$this->Modified = true;
			}

			if (isset($this->ProcessData['OwnerID']) and ($this->ProcessData['OwnerID'] != $this->OwnerID))
			{
				$this->OwnerID = $this->ProcessData['OwnerID'];
				$this->Owner = null;
				$this->Modified = true;
			}

			if (isset($this->ProcessData['ParentID']) and ($this->ProcessData['ParentID'] != $this->ParentID))
			{
				$this->ParentID = $this->ProcessData['ParentID'];
				$this->Parent = null;
				$this->Modified = true;
			}

			if (isset($this->ProcessData['Code']) and ($this->ProcessData['Code'] != $this->Code))
			{
				$this->Code = $this->ProcessData['Code'];
				$this->Modified = true;
			}

			return $this->Modified;
		}

		protected function SaveAction()
		{
			$this->Refresh();

			if ($this->SetActionData()) 
			{
				$result = $this->Save(); 
			}
			else $result = true;

			return $result;
		}

		protected function DeleteAction()
		{
			return $this->Delete();
		}

		protected function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			$this->Refresh();
		}      

		public function __toString()
		{
			return $this->Description;
		}
		
		public function __get($FieldName)
		{
			ErrorHandle::ErrorHandle('Обращение к несуществующему полю '.$FieldName.' объекта '.get_class($this).'.',2);
		}
		
		
		
	}
?>
