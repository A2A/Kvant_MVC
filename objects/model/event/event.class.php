<?php
	class Event extends Entity
	{
		protected $DBTableName = 'events';

		public $Project;
		public $ProjectID;
		public $Task;
		public $TaskID;

		public $ContractorID;
		public $Contractor;

		public $InitDate;
		public $InitDateText = null;
		public $FinishDate = null;
		public $FinishDateText = null;

		public $StartDateText = null;

		public $FullDescription = '';
		public $ClassID;
		public $Continue = 1; // 1 - не завершено, 0 - завершено

		public $DRU;
		public $DRUID;

		public $CurrentTime;

		public static $Forms = array(
		'edit' 		=> 'objects/model/event/edit.html',
		'view' 		=> 'objects/model/event/view.html',
		'new' 		=> 'objects/model/event/new.html',
		'shortinfo' => 'objects/model/event/short_info.html',
		'desktop' 	=> 'objects/model/event/list_type_page.html',
		);

		public static $SQLFields = array(
		'ID' => 'ID',
		'Description' => 'DESCRIPTION',
		'ClassID' => 'CLASSID',
		'TaskID' => 'TASKID',
		'ProjectID' => 'PROJECTID',
		'Continue' => 'CONTINUE'
		);

		// TODO 3 -o Natali -c Функционал: переписать SetActionData() 
		protected function SetActionData()
		{
			parent::SetActionData();

			if (isset($this->ProcessData['Description']))
			{
				$this->Description = $this->ProcessData['Description'];
				$this->Modified = true;
			}

			if (isset($this->ProcessData['ContractorID']) and ($this->ProcessData['ContractorID'] != $this->ContractorID))
			{
				$this->ContractorID = $this->ProcessData['ContractorID'];
				$this->Contractor = null;
				$this->Modified = true;
			}
			if (isset($this->ProcessData['ProjectID']) and ($this->ProcessData['ProjectID'] != $this->ProjectID))
			{
				$this->ProjectID = $this->ProcessData['ProjectID'];
				$this->Project = null;
				$this->Modified = true;
			}
			if (isset($this->ProcessData['TaskID']) and ($this->ProcessData['TaskID'] != $this->TaskID))
			{
				$this->TaskID = $this->ProcessData['TaskID'];
				$this->Task = null;
				$this->Modified = true;
			}
			if (isset($this->ProcessData['EventTypeID']) and ($this->ProcessData['EventTypeID'] != $this->TypeID))
			{
				$this->ClassID = $this->ProcessData['EventTypeID'];
				$this->Owner = null;
				$this->Modified = true;
			}

			// TODO 1 -o Nat -c Заменить: UserID and RoleID -> DRUID
			if (isset($this->ProcessData['DRUID']) and ($this->ProcessData['DRUID'] != $this->DRUID))
			{
				$this->DRUID = $this->ProcessData['DRUID'];
				$this->DRU = null;
				$this->Modified = true;
			}
			if (isset($this->ProcessData['InitDate']) and ($this->ProcessData['InitDate'] != $this->InitDate))
			{
				$this->InitDate = $this->ProcessData['InitDate'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['Continue']) and ($this->ProcessData['Continue'] != $this->Continue))
			{
				$this->Continue = $this->ProcessData['Continue'];
				$this->Modified = true;
			}
			$this->FinishDate = mktime(); 

			return $this->Modified;

		}
		// TODO 3 -o Natali -c Функционал: переписать  Refresh()
		public function Refresh()
		{
			$null = null;
			if (intval($this->ID))
			{
				$sql = 'Select * from '.$this->DBTableName.' where ID = '.$this->ID;
				
				$hSql = DBMySQL::Query($sql);
				while ($fetch = DBMySQL::FetchObject($hSql)) 
				{
					$this->Description = $fetch->DESCRIPTION;
					if ($this->ClassID != intval($fetch->CLASSID))
					{
						$this->ClassID = intval($fetch->CLASSID);
						if (!is_null($this->ClassID))
						{
							$this->Owner = TPEClass::GetObject($null,$this->ClassID);
						}
						else
						{
							$this->Owner = null;
						}

					}
					if ($this->ProjectID != intval($fetch->PROJECTID))
					{
						$this->ProjectID = intval($fetch->PROJECTID);
						if (!is_null($this->ProjectID))
						{
							// TODO 1 -o Nata -c Project: не функционален
							//$this->Project = Project::GetObject($null,$this->ProjectID);
						}
						else
						{
							$this->Project = null;
						}
					}

					if ($this->TaskID != intval($fetch->TASKID))
					{
						$this->TaskID = intval($fetch->TASKID);
						if (!is_null($this->TaskID))
						{
								// TODO 1 -o Nata -c Task: не функционален
								//$this->Task = Task::GetObject($null,$this->TaskID);
						}
						else
						{
							$this->Task = null;
						}
					}

					$this->InitDate = strtotime($fetch->DATE_INIT);
					$this->InitDateText = DateTimeToStr($this->InitDate);

					$this->FinishDate = strtotime($fetch->DATE_FINISH); 

					if ($this->DRUID != intval($fetch->DRUID))
					{
						$this->DRUID = intval($fetch->DRUID);
						if (!is_null($this->DRUID))
						{
							$ClassName = 'DRU';
							// TODO 2 -o Natali -c Связь с БД: создание DRU для события
							$this->DRU = DRU::GetObject($null,$this->DRUID);
						}
						else
						{
							$this->DRU = null;
						}
					}
					$this->Continue = $fetch->CONTINUE; 
				}
				
			}
			else
			{
				$this->InitDate = mktime();
				$this->InitDateText = DateTimeToStr($this->InitDate);
				$this->FinishDate = mktime();  
				   
			}
			$this->FinishDateText = DateTimeToStr($this->FinishDate);  
		
			$this->CurrentTime =  DateTimeToStr(mktime());  
			$this->SetActionData();   
		}

		// TODO 3 -o Natali -c Функционал: переписать update в Save()  
		public function SaveAction()
		{
			if (!intval($this->ID))
			{
				$sql = 'insert into '.$this->DBTableName.' (ID, DESCRIPTION,
				`CLASSID`,`PROJECTID`,`DATE_INIT`,`DATE_FINISH`,
				`DRUID`,`DURATION_PAUSE`,`TASKID`,`CONTINUE`)
				values (NULL,"'.$this->Description.'"
				,"'.$this->ClassID.'",'.(is_numeric($this->ProjectID)?$this->ProjectID:'null').',
				"'.DateTimeToMySQL($this->InitDate).'","'.DateTimeToMySQL($this->FinishDate).'",
				'.(is_numeric($this->DRUID)?$this->DRUID:'null').',
				'.($this->Pause=0).','.(is_numeric($this->TaskID)?$this->TaskID:'null').','.$this->Continue.')';
				// TODO 10 -o N -c Сообщение для отладки: SQL
				ErrorHandle::ErrorHandle($sql);

				$hSql = DBMySQL::Query($sql);
				if ($hSql)
				{
					$this->ID = DBMySQL::InsertID($hSql);
					$this->ChangedFields[] = array('name' => 'ID','value' => $this->ID);
					ErrorHandle::ErrorHandle('Объект типа '.get_class($this).' успешно сохранен.',0);
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
				PARENTID='.(intval($this->ParentID)?intval($this->ParentID):'null').',
				PROJECTID='.(intval($this->ProjectID)?intval($this->ProjectID):'null').',
				DATE_INIT="'.DateTimeToMySQL($this->InitDate).'",
				DATE_START="'.DateTimeToMySQL($this->StartDate).'",
				DATE_FINISH="'.DateTimeToMySQL($this->FinishDate).'",
				FULL_DESCR="'.$this->FullDescr.'", 
				MANAGERID='.(intval($this->OwnerID)?intval($this->OwnerID):'null').', 
				USERID='.(intval($this->UserID)?intval($this->UserID):'null').' 
				where ID = '.$this->ID;
				// TODO 10 -o N -c Сообщение для отладки: SQL
				ErrorHandle::ErrorHandle($sql);

				$hSql = DBMySQL::Query($sql);
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

		public function __get($FieldName)
		{
			switch ($FieldName)
			{
				case 'Time' :	return date('H:i',$this->InitDate);
				case 'Date' : 	return date('d.m.Y',$this->InitDate);
				case 'Duration' : return gmdate('H:i',($this->FinishDate-$this->InitDate));
				case 'UserRole' : return $this->DRU->RoleDescr;
				case 'User' 	: return $this->DRU->UserDescr;
			}

		}

		static public function GetSQLField($Field)
		{
			return Event::$SQLField[$Field];
		}

		public function __construct(&$ProcessData,$ID=null)  
		{   
			parent::__construct($ProcessData,$ID);
			$this->Refresh();     
		}

		static public function GetObject(&$ProcessData,$ID=null)
		{
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}
	}

?>
