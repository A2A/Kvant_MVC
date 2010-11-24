<?php
	class TPStatusList extends CollectionDB
	{
		protected $DBTableName = 'tp_status';    
		public static $Forms = array(
		'list' => 'objects/model/tp_status/list.html',
		'list_pt' => 'objects/model/tp_status/list_pt.html',
		);
		
		public $_ValueType = "TPStatus";
		
		
		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,'TPStatus');
			$this->Refresh();
	 
		}
		
		public function Refresh()
		{
			if($this->ProcessData['Filter']['0']['Field'] == 'ProjectID')
			 {
				$Sql = 'select * from `project_status_tl` where `PROJECTID` = '.$this->ProcessData['Filter']['0']['Val'] .' order by CHANGEDATE';
				
				$hSql = DBMySQL::Query($Sql);
				if ($hSql)
				{
					$ClassName = $this->_ValueType;
					while ($fetch = DBMySQL::FetchObject($hSql)) 
					{
						if ($TPStatus = TPStatus::GetObject($null,$fetch->STATUSID) and $User = User::GetObject($null,$fetch->USERID))
						{
							$TPStatus->User = &$User;
							$TPStatus->DateSet = DateTimeToStr(strtotime($fetch->CHANGEDATE));
							$this->add($TPStatus);
						}
					}
				}
				else
				{
					ErrorHandle::ActionErrorHandle('Ошибка при получении списка статусов проекта.',2);
					$Result = true;
				}	 
				
			 }
			 elseif($this->ProcessData['Filter']['0']['Field'] == 'TaskID')
			 {
				$Sql = 'select * from `task_status_tl` where `TASKID` = '.$this->ProcessData['Filter']['0']['Val'] .' order by CHANGEDATE';
				
				$hSql = DBMySQL::Query($Sql);
				if ($hSql)
				{
					$ClassName = $this->_ValueType;
					while ($fetch = DBMySQL::FetchObject($hSql)) 
					{
						if ($TPStatus = TPStatus::GetObject($null,$fetch->STATUSID) and $User = User::GetObject($null,$fetch->USERID))
						{
							$TPStatus->User = &$User;
							unset($User);
							$TPStatus->DateSet = DateTimeToStr(strtotime($fetch->CHANGEDATE));
							$this->add($TPStatus);
						}
					}
				}
				else
				{
					ErrorHandle::ActionErrorHandle('Ошибка при получении списка статусов задачи.',2);
					$Result = true;
				}	 
				
			 }
			
		}
		
		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}
	  
	}  
?>
