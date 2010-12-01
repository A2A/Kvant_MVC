<?php
	class Task extends Entity
	{
		protected $DBTableName = 'tasks';
		public $ReadyState = 100;

		public $Project;
		public $ProjectID;

		public $ContractorID;
		public $Contractor;

		public $InitDate;
		public $StartDate = null;
		public $FinishDate = null;

		public $OwnerType = 'User';

		public $FullDescription = '';

		public $User;
		protected $UserID;

		public $CurrentStatusID = 1;
		public $CurrentStatus = 'Поставлена';



		// TODO 4 -o Natali -c Замечание: форма edit и view_full, если запрошено форма редактирования, а пользователь не имеет рава редактировать этот проект, то надо отдавать форму view_full - полное описание 

		public static  $Forms = array(
		'edit' 			=> 'objects/model/task/edit.html',
		'shortinfo' 	=> 'objects/model/task/short_info.html',
		'view_short' 	=> 'objects/model/task/view_short.html',
		'view_status' 	=> 'objects/model/task/view_status.html',
		'view_full' 	=> 'objects/model/task/view_full.html',
		'new' 			=> 'objects/model/task/new.html',
		);

		public static  $SQLFields = array(
		'ID' => 'ID',
		'Description' => 'DESCRIPTION',
		'OwnerID' => 'OWNERID',
		'ParentID' => 'PARENTID',
		'ProjectID' => 'PROJECTID'
		);

		public function GetState()
		{
			$FT = ($this->FinishDate - $this->StartDate);
			$CT = mktime() - $this->StartDate;
			//echo $FT." секунды = (".$this->FinishDate." - ".$this->StartDate.")<hr>";

			@ $Status = $this->ReadyState * $FT / $CT;
			if ( $Status > 110 ) 
				return 'green';
			elseif ( $Status > 98 ) 
				return 'yellow';
			else 
				return 'red';
		}

		public function GetDuration()
		{
			$W = 604800;
			$D = 86400;
			$H = 3600;
			$M = 60;

			$WD_Start = 9*$H + 0*$M;
			$WD_Finish = 18*$H + 0*$M;

			$Start00 = GetBeginOfDay($this->InitDate);
			$Start09 = GetBeginOfWorkDay($this->InitDate);
			$Start18 = GetEndOfWorkDay($this->InitDate);

			$WD_Duration = $Start18 - $Start09; 


			$Start = max($this->InitDate,$Start09);
			if ($Start > $Start18) $Start = $Start09 + $D;

			$Finish00 = GetBeginOfDay($this->FinishDate);
			$Finish09 = GetBeginOfWorkDay($this->FinishDate);
			$Finish18 = GetEndOfWorkDay($this->FinishDate);

			$Finish = min($this->FinishDate,$Finish18);
			if ($Finish < $Finish09)  $Finish  = $Finish18 - $D;

			if ($Finish <=$Start) return 0;

			$DurationTotal = ($Finish - $Start);

			$Days = $DurationTotal % $D;

			$Tail = $DurationTotal - $Days * $D;

			if ($Tail > $D - $WD_Duration)
			{
				$Days = $Days +1;
				$Tail = $Tail - ($D - $WD_Duration);
			}

			$Houres = $Tail % $H;
			$Tail = $Tail - $Houres * $H;

			$Minutes = $Tail % $M;

			$TimeLine = '';
			if ($Days > 0)      $TimeLine = $Days.' дней';
			if ($Houres > 0)    $TimeLine = $TimeLine . ($TimeLine = ''?'':' ').$Houres.' часов';
			if ($Minutes > 0)   $TimeLine = $TimeLine . ($TimeLine = ''?'':' ').$Minutes.' минут';

			$TimeLine = $TimeLine . ($TimeLine = ''?'':'.');
			return $TimeLine;
		}

		protected function SetActionData()
		{
			parent::SetActionData();

			if (isset($this->ProcessData['FullDescription']))
			{
				$this->FullDescription = $this->ProcessData['FullDescription'];
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
			
			if (isset($this->ProcessData['UserID']) and isset($this->ProcessData['RoleID']))
			{
				// TODO 10 -o Natali -c Функцианал: получить ДРУ в js или в пхп, сейчас берется просто от выбраной роли исполнителя.
				$this->DRUID = $this->ProcessData['RoleID'];
			}
			
			
			if (isset($this->ProcessData['StartDateValue']) and ($this->ProcessData['StartDateValue'] != $this->StartDate))
			{
				$this->StartDate = $this->ProcessData['StartDateValue'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['FinishDateValue']) and ($this->ProcessData['FinishDateValue'] != $this->FinishDate))
			{
				$this->FinishDate = $this->ProcessData['FinishDateValue'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['FullDescription']) and ($this->ProcessData['FullDescription'] != $this->FullDescription))
			{
				$this->FullDescription = $this->ProcessData['FullDescription'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['ReadyState']) and ($this->ProcessData['ReadyState'] != $this->ReadyState))
			{
				$this->ReadyState = $this->ProcessData['ReadyState'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['TPEClass']) and ($this->ProcessData['TPEClass'] != $this->TPEClass))
			{
				$this->TPEClass = $this->ProcessData['TPEClass'];
				$this->Modified = true;
			}
			if (isset($this->ProcessData['StatusID']) and ($this->ProcessData['StatusID'] != $this->StatusID))
			{
				$this->StatusID = $this->ProcessData['StatusID'];
				$this->Modified = true;
			}
			
			return $this->Modified;

		}

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
					if ($this->ParentID != intval($fetch->PARENTID))
					{
						$this->ParentID = intval($fetch->PARENTID);
						if (!is_null($this->ParentID))
						{
							$this->Parent = Task::GetObject($null,$this->ParentID);
						}
						else
						{
							$this->Parent = null;
						}
					}
					if ($this->ProjectID != intval($fetch->PROJECTID))
					{
						$this->ProjectID = intval($fetch->PROJECTID);
						if (!is_null($this->ProjectID))
						{
							$this->Project = Project::GetObject($null,$this->ProjectID);
						}
						else
						{
							$this->Project = null;
						}
					}

					$tmpDate = strtotime($fetch->DATE_INIT);
					$this->InitDate = strtotime($fetch->DATE_INIT);
					$this->InitDateText = DateTimeToStr($this->InitDate);

					$tmpDate  = strtotime($fetch->DATE_START);
					$this->StartDate = $tmpDate ;

					$tmpDate  = strtotime($fetch->DATE_FINISH);
					$this->FinishDate = $tmpDate ;

					$this->FullDescription = $fetch->FULL_DESCR;
					$this->CurrentStatusID = (is_numeric($fetch->STATUSID)?$fetch->STATUSID:1);

					/*
					// TODO 4 -o Natali -c Логика создания и хранения: выбрали ДРУ постановщика - оно нам важно в показателях или нет?
					$sql="select USERID from `dru` where ID=".intval($fetch->DRUID);
					$UserSql = DBMySQL::Query($sql);
					if ($Userfetch = DBMySQL::FetchObject($UserSql) and is_numeric($Userfetch->USERID))
					{
						$this->ManagerID = $Userfetch->USERID;
						$this->Manager = new User($null,$this->ManagerID);
						$this->Manager->Refresh();
					}
					else
					{
						$this->ManagerID = null;
						$this->Manager   = "Постановщик не установлен";
					}
					*/  
					if ($this->ManagerID != intval($fetch->MANAGERID))
					{
						$this->ManagerID = intval($fetch->MANAGERID);
						if (!is_null($this->ManagerID))
						{
							$ClassName =  'User';
							$this->Manager = $ClassName::GetObject($null,$this->ManagerID);
						}
						else
						{
							$this->Manager = null;
						}
					}
					

					if (isset($fetch->CONTRACTORID) and $this->ContractorID != intval($fetch->CONTRACTORID))
					{
						$this->ContractorID = intval($fetch->CONTRACTORID);
						if (!is_null($this->ContractorID))
						{
							$ClassName =  'Contractor';
							$this->Contractor = $ClassName::GetObject($null,$this->ContractorID);
						}
						else
						{
							$this->Contractor = null;
						}
					}
					
					if (isset($fetch->DRUID) and  $this->DRUID != intval($fetch->DRUID))
					{
						$this->DRUID = intval($fetch->DRUID);
						if (!is_null($this->DRUID))
						{
							$ClassName = "DRU";
							$this->DRU = $ClassName::GetObject($null,$this->DRUID);
						}
						else
						{
							$this->DRU = null;
						}
					}

					if (is_null($fetch->READY_STATE) or $fetch->READY_STATE == "")
						$this->ReadyState = 0;
					else
						$this->ReadyState = $fetch->READY_STATE; 


				}
			}
			else
			{
				$this->Manager = User::GetObject($null,$this->CurrentUserID);
				$this->ManagerID = $this->CurrentUserID;

				$tmpDate = mktime();
				$this->InitDate = $tmpDate;
				$this->InitDateText = DateTimeToStr($this->InitDate);

				$this->StartDate = $tmpDate ;

				$this->FinishDate = $tmpDate ;
				$this->ReadyState = 0; 
				//print_r($this); 

			}

		}

		public function SaveAction()
		{
			if ($this->SetActionData()) 
			{
				$Result = $this->Save(); 
			}
			else 
				$Result = true;
				
			return $Result;
		}
		
		public function Save()
		{
			if (!intval($this->ID))
			{
				// TODO 4 -o Natali -c Ошибка формирования SQL запроса: при создании если не установлен пользователь, надо получить текущего для $this->UserID
				$sql = 'insert into '.$this->DBTableName.' (
				`ID`,`DESCRIPTION`,`PARENTID`,`PROJECTID`,
				`DATE_INIT`,`DATE_START`,`DATE_FINISH`,
				`FULL_DESCR`,`MANAGERID`,`DRUID`,
				`READY_STATE`,`STATUSID`,`CLASSID`,`CONTRACTORID`) 
				values 
				(NULL,"'.$this->Description.'",'.((intval($this->ParentID)>0)?intval($this->ParentID):'null').', '.(intval($this->ProjectID)?intval($this->ProjectID):'null').',
				"'.DateTimeToMySQL($this->InitDate).'","'.DateTimeToMySQL($this->StartDate).'","'.DateTimeToMySQL($this->FinishDate).'",
				"'.$this->FullDescription.'",'.(intval($this->ManagerID)?intval($this->ManagerID):'null').','.(intval($this->DRUID)?intval($this->DRUID):'null').',
				"'.$this->ReadyState.'",1,'.(intval($this->StatusID)?intval($this->StatusID):'null').','.(intval($this->ContractorID)?intval($this->ContractorID):'null').')';
				 

				// TODO 4 -o Natali -c сообщение для отладки: SQL  
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
				FULL_DESCR="'.$this->FullDescription.'", 
				READY_STATE="'.intval($this->ReadyState).'", 
				CONTRACTORID='.(intval($this->ContractorID)?intval($this->ContractorID):'null').', 
				DRUID='.(intval($this->DRUID)?intval($this->DRUID):'null').' 
				where ID = '.$this->ID;
			 

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
			return $Result;
		}      

		public function GetLevel()
		{
			$null = null;
			if (is_null($this->ParentID)) return 1;
			elseif (is_null($this->Parent))
			{
				$ClassName = get_class($this);                    
				$this->Parent = $ClassName::GetObject(null,$this->ParentID);
			}
			return $this->Parent->Level+1;
		}



		public function __get($FieldName)
		{
			switch (strtolower($FieldName))
			{
				case 'state' 		: return $this->GetState();
				case 'level' 		: return $this->GetLevel();
				case 'duration' 	: return $this->GetDuration();
				case 'startdatetext': return date('H.i d.m.y',$this->StartDate);
				case 'finishdatetext': return date('H.i d.m.y',$this->FinishDate);
				case 'initdatetext'	: return date('H.i d.m.y',$this->InitDate);
				case 'statebegin'       :{    
					if ($this->StartDate < $_SESSION['CurrentWPTime'] and $_SESSION['CurrentWPTime'] < $this->FinishDate)
						return $this->State."-TimeLine-begin";
					elseif ($_SESSION['CurrentWPTime'] > $this->FinishDate)
						return $this->State."-TimeLine-end";  
					else
						return "None";
				}
				case 'stateend'         :{   
					
					$int = new Interval($null,$_SESSION['CurrentIntID']);     
					$EndTime = $_SESSION['CurrentWPTime'] + 16 * $int->Duration; 

					if ($this->InitDate < $EndTime and $EndTime < $this->FinishDate)
					{   
						return $this->State."-TimeLine-end $EndTime";
					}
					elseif ($EndTime < $this->InitDate and $EndTime < $this->StartDate)
					{   
						return $this->State."-TimeLine-begin $EndTime"; 
					} 
					else
						return "None ".$_SESSION['CurrentWPTime'] ." ==".$int->Duration." < $EndTime <".$this->FinishDate;
				}
				default : return null;
			}

		}

		static public function GetSQLField($Field)
		{
			return Task::$SQLFields[$Field];
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
			return static::GetObjectInstance($ProcessData,$ID,__CLASS__);
		}

	}

?>
