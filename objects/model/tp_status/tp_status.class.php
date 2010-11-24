<?php
	class TPStatus extends Entity 
	{
		protected $DBTableName = 'tp_status';    
		public static $Forms = array(
		'description' => 'objects/model/tp_status/description.html'      
		);

		
		public $User;
		public $DateSet;
		protected $ProjectID;
		protected $TaskID;
		protected $CurrentUserID;
		
		public static $SQLFields = array(
		'ID' => 'ID',
		'Description' 	=> 'DESCRIPTION'
		);
		
		protected function SetActionData()  
		{
			if (isset($this->ProcessData['ProjectID']) and ($this->ProcessData['ProjectID'] != $this->ProjectID))
			{
				$this->ProjectID = $this->ProcessData['ProjectID'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['TaskID']) and ($this->ProcessData['TaskID'] != $this->TaskID))
			{
				$this->TaskID = $this->ProcessData['TaskID'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['StatusID']) and ($this->ProcessData['StatusID'] != $this->ID))
			{
				$this->ID = $this->ProcessData['StatusID'];
				$this->Modified = true;
			}
		
		}
		
		public function SaveAction()  
		{
			$this->SetActionData();
			if (intval($this->ProjectID) and intval($this->ID))
			{
				$Sql = 'select SET_PROJECT_STATUS('.$this->ProjectID.','.$this->ID.','.$this->CurrentUserID.') as Result';
				
				$hSql = DBMySQL::Query($Sql);
				if ($hSql)
				{
					ErrorHandle::ActionErrorHandle('Статус проекта успешно изменен.',0);
					$Result = true;
				}
				else
				{
					ErrorHandle::ActionErrorHandle('Ошибка изменения статуса проекта.',2);
					$Result = true;
				}
			}
			elseif (intval($this->TaskID) and intval($this->ID))
			{
				$Sql = 'select SET_TASK_STATUS('.$this->TaskID.','.$this->ID.','.$this->CurrentUserID.') as Result';
				$hSql = DBMySQL::Query($Sql);
				if ($hSql)
				{
					ErrorHandle::ActionErrorHandle('Статус задачи успешно изменен.',0);
					$Result = true;
				}
				else
				{
					ErrorHandle::ActionErrorHandle('Ошибка изменения статуса задачи.',2);
					$Result = true;
				}
			}
			return $Result;
		}
		
		public static function GetSQLField($Field)
		{
			return TPStatus::$SQLFields[$Field];
		}
		
		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			$System = System::GetObject();
			$this->CurrentUserID = $System->CurrentUserID;
			unset($System);
			$this->Refresh();     
		}

		static public function GetObject(&$ProcessData,$ID=null)
		{
			return new TPStatus($ProcessData,$ID);   
			//return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}

	}  
?>
